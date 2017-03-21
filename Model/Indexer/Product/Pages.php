<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SuttonSilver\WordpressProductPage\Model\Indexer\Product;


class Pages implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{

    protected  $_fishpigPost;
    protected $_productRepositoryFactory;
    protected $_storeManager;
    protected $_fishpigPostCollection;
    protected $_eavConfig;
    protected $_logger;
    protected $_catalogCollection;

    public function __construct
    (
        \SuttonSilver\WordpressProductPage\Model\PostFactory $fishpigPost,
        \Magento\Framework\Logger\Monolog $logger, //log injection
        \FishPig\WordPress\Model\ResourceModel\Post\CollectionFactory $fishpigPostCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $catalogCollection,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->_fishpigPost = $fishpigPost;
        $this->_fishpigPostCollection = $fishpigPostCollection;
        $this->_productRepositoryFactory = $productRepositoryFactory;
        $this->_catalogCollection = $catalogCollection;
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        $this->_logger = $logger;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->executeFull();
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $collectionProducts = $this->_catalogCollection->create();
        $collectionPages = $this->_fishpigPostCollection->create()->addPostTypeFilter('products');
        $repo = $this->_productRepositoryFactory->create();

        $pageIds = [];
        $associatedIds = [];

        //loop over product collection
        foreach($collectionProducts as $cproduct)
        {
            //get id from original loop
            $id = $cproduct->getId();

            //create repo and get product
            $product = $repo->getById($id,true);

            //create wp-page
            $pId = $this->createUpdateWpPage($product);

            //set associated
            $product->setData('associated_page', $pId);

            try {
                //repo save product
                $repo->save($product);
            }catch(\Exception $e)
            {
                $this->_logger->addInfo($e->getMessage());
            }

            //get associated id
            $associatedIds[] = $pId;

        }

        foreach($collectionPages as $page)
        {
            $pageIds[] = $page->getId();
        }

        $diff = array_diff($pageIds,$associatedIds );
        $this->deleteWpPages($diff);

        $this->_logger->addDebug('Page Ids:'.print_r($pageIds,true));
        $this->_logger->addDebug('Associated Ids:'.print_r($associatedIds,true));
        $this->_logger->addDebug('Unassociated Ids:'.print_r($diff,true));

    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        foreach($ids as $p)
        {
            $product = $this->_productRepositoryFactory->create();
            $product = $product->getById($p);
            $associatedIds[] = $this->createUpdateWpPage($product);
        }
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $product = $this->_productRepositoryFactory->create()->getById($id);
        $this->createUpdateWpPage($product);
    }

    public function createUpdateWpPage($product)
    {
        $url =$this->_storeManager->getStore()->getBaseUrl();

        $newPost = null;
        $newPost = $this->_fishpigPost->create();

        if($product->getData('associated_page') == $this->getPostByKey($product)) {
            $newPost->load($product->getData('associated_page'));
        }

        $newPost->setPostTitle($product->getName());

        $newPost->setPostType('products');
        $newPost->setPostName('wordpress-'.$product->getUrlKey());
        $newPost->setGuid($url.'?p=' . $newPost->getId() . '&post_type=' . $newPost->getPostType());
        $newPost->setPostStatus('publish');
        $newPost->setPostDate(date('Y/m/d'));

        try {
            $newPost->save();
        }catch(\Exception $e)
        {
            $this->_logger->addInfo($e->getMessage());
        }


        return $newPost->getId();

    }

    public function getPostByKey($product){
        $collectionPages = $this->_fishpigPostCollection->create()
            ->addPostTypeFilter('products')
            ->addFieldToFilter('post_name',$product->getUrlKey())
            ->addFieldToFilter('post_title',$product->getName())
            ->getFirstItem()
            ->getId();
        return $collectionPages;
    }

    public function deleteWpPages($ids)
    {
        foreach($ids as $id) {
            $newPost = null;
            $newPost = $this->_fishpigPost->create();
            $newPost->load($id);
            $newPost->setPostStatus('trash');
            $newPost->save();
        }

    }


}

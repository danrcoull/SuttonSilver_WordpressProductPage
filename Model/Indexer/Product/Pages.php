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
        \FishPig\WordPress\Model\Post $fishpigPost,
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

        $pageIds = [];
        $associatedIds = [];

        $productRepo = $this->_productRepositoryFactory->create();

        foreach($collectionProducts as $product)
        {
            $id = $product->getId();

            $fullproduct = $productRepo->getById($id);

            $associatedIds[] = $pId = $this->createUpdateWpPage($fullproduct);

            $product->setData('associated_page', $pId);
            $product->setData('url_key', $fullproduct->getUrlKey());
            $product->save();

        }

        foreach($collectionPages as $page)
        {
            $pageIds[] = $page->getId();
        }

        $diff = array_diff($pageIds,$associatedIds );
        $this->deleteWpPages($diff);

    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $product = $this->_productRepositoryFactory->create();
        foreach($ids as $id)
        {
            $fullproduct = $product->getById($id);
            $id =  $this->createUpdateWpPage($fullproduct);
            $product->setData('associated_page', $id);
            $product->setData('url_key', $fullproduct->getUrlKey());
            $product->save();
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
        $id = $this->createUpdateWpPage($product);
        $product->setData('associated_page', $id);
        $product->setData('url_key', $product->getUrlKey());
        $product->save();
    }

    public function createUpdateWpPage($product)
    {
        $url =$this->_storeManager->getStore()->getBaseUrl();

        $newPost = $this->_fishpigPost->load($product->getData('associated_page'));

        $newPost->setPostTitle($product->getName());

        $newPost->setPostType('products');
        $newPost->setPostName('wordpress-'.$product->getUrlKey());
        $newPost->setGuid($url.'?p=' . $newPost->getId() . '&post_type=' . $newPost->getPostType());
        $newPost->setPostStatus('publish');
        $newPost->save();

        return $newPost->getId();

    }

    public function deleteWpPages($ids)
    {
        $fpPost =$this->_fishpigPost;

        foreach($ids as $id) {

            $newPost = $fpPost->load($id);
            $newPost->setPostStatus('trash');
            $newPost->save();
        }

    }


}

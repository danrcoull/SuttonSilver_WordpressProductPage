<?php

namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\ProductList;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related {

    protected $_fishpig;
    protected $_filter;
    protected $_postData;
    protected $productRepository;

    private $productId;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \FishPig\WordPress\Model\App\Factory $factory,
        \SuttonSilver\WordpressProductPage\Helper\Filter $filter,
        \Magento\Framework\Data\Helper\PostHelper $postData,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {


        $this->_fishpig = $factory;
        $this->_filter = $filter;
        $this->_postData = $postData;
        $this->productRepository = $productRepository;

        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }

    protected function _prepareData()
    {
        if($this->getProductId())
        {
            $product = $this->productRepository->getById($this->getProductId());
        }else {
            $product = $this->_coreRegistry->registry('product');
            /* @var $product \Magento\Catalog\Model\Product */
        }

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function getPostById($id)
    {
        $post = $this->_fishpig->getFactory('Post')->create()->load($id);
        return $post;
    }

    public function getPostData($item)
    {
        $postData = $this->_postData->getPostData($this->getAddToCartUrl($item), ['product' => $item->getEntityId()]);
        return $postData;
    }

    public function getProductId(){
        return $this->productId;
    }

    public function setProductId($id)
    {
        $this->productId = $id;
        return $this;
    }

	public function getTaxonomy()
	{
		return  $this->_fishpig->getFactory('Term\Taxonomy')->create();
	}

}
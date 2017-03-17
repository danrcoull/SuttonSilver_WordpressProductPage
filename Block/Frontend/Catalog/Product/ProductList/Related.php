<?php

namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\ProductList;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related {

    protected $_fishpig;
    protected $_filter;
    protected $_postData;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \FishPig\WordPress\Model\App\Factory $factory,
        \SuttonSilver\WordpressProductPage\Helper\Filter $filter,
        \Magento\Framework\Data\Helper\PostHelper $postData,
        array $data = []
    ) {


        $this->_fishpig = $factory;
        $this->_filter = $filter;
        $this->_postData = $postData;

        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
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
}
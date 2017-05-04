<?php
namespace SuttonSilver\WordpressProductPage\Block\Frontend\CMS;

class Frontpage extends \Magento\Framework\View\Element\Template
{


    protected $_fishpig;
    protected $_filter;
    protected $_config;
    protected $_app;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FishPig\WordPress\Block\Context $wpContext,
        \SuttonSilver\WordpressProductPage\Helper\Filter $filter,
        array $data = []
    )
    {
        $this->_fishpig = $wpContext->getFactory();
        $this->_filter = $filter;
        $this->_config = $wpContext->getConfig();
        $this->_app = $wpContext->getApp();

        parent::__construct($context, $data);
    }

    public function getFrontPage()
    {
        $id = $this->_app->getHomepagePageId();
        $post = $this->_fishpig->getFactory('Post')->create()->load($id);
        return $post;
    }
}
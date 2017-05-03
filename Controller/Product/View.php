<?php

namespace SuttonSilver\WordpressProductPage\Controller\Product;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Framework\App\Action\Action
{

    protected $_resultJsonFactory;
    protected $layoutFactory;
    protected $productRepository;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        ProductRepositoryInterface $productRepository
    )
    {

        $this->_resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }


    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $data = [];
        if ($this->getRequest()->isAjax()) {
            $layout = $this->layoutFactory->create();
            $productId= $this->getRequest()->getParam('productId');
            $product = $this->productRepository->getById($productId);

            $title = $layout ->createBlock('\Magento\Theme\Block\Html\Title');
            $title->setPageTitle($product->getName());
            $title = $title->setTemplate('Magento_Theme::html/title.phtml')->toHtml();

            $main = $layout
                ->createBlock('\SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\View')
                ->setProductId($productId)
                ->setTemplate('SuttonSilver_WordpressProductPage::product/view/content.phtml')
                ->toHtml();




            $priceDefault = $layout->createBlock('\Magento\Framework\Pricing\Render',
                "product.price.render.default",
                ["data" => ['price_render_handle' => 'catalog_product_prices', 'use_link_for_as_low_as'=>true]]
            );

            $price = $layout->createBlock('\Magento\Catalog\Pricing\Render',
                "product.price.tier",
                ["data" => ['price_render' => 'product.price.render.default', 'price_type_code'=>'final_price','zone'=>'item_view']]
            )->setChild('child',$priceDefault);

            $main2 = $layout
                ->createBlock('\SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\ProductList\Related')
                ->setProductId($productId)
                ->setType('related')
                ->setTemplate('SuttonSilver_WordpressProductPage::product/list/items.phtml')
                ->setChild('child',$price)
                ->toHtml();


            $layout->addContainer('product.info.sidebar','sidebar');
            $layout->setChild('product.info.sidebar','product.price.tier','price.sidebar');

            $sidebar = $layout
                ->createBlock('\SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\View')
                ->setProductId($productId)
                ->setName('product.info.content.addtocart')
                ->setTemplate('SuttonSilver_WordpressProductPage::product/simplemodal/popup-addtocart.phtml')
                ->setChild('child','product.info.sidebar')
                ->toHtml();


            $data['title'] = $title;
            $data['main'] = $main.$main2;
            $data['sidebar'] = $sidebar;

        }

        return $result->setData($data);
    }
}
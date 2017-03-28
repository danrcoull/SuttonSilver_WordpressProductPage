<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product\View\Type;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;

/**
 * Catalog bundle product info block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Bundle extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle
{

    protected $_fishpig;
    protected $_filter;

    private $productId;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Bundle\Model\Product\PriceFactory $productPrice,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \FishPig\WordPress\Model\App\Factory $factory,
        \SuttonSilver\WordpressProductPage\Helper\Filter $filter,
        array $data = []
    )
    {



        $this->_fishpig = $factory;
        $this->_filter = $filter;
        parent::__construct(
            $context,
            $arrayUtils,
            $catalogProduct,
            $productPrice,
            $jsonEncoder,
            $localeFormat,
            $data
        );
    }

    public function getPost() {
        if($this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
        }else {
            $product = $this->getProduct();
        }
        $id = $product->getData('associated_page');

        $factory = $this->_fishpig->getFactory('Post')->create();
        $post = $factory->load($id);
        return $post;
    }

    public function getPostById($id)
    {
        $post = $this->_fishpig->getFactory('Post')->create()->load($id);
        return $post;
    }
}

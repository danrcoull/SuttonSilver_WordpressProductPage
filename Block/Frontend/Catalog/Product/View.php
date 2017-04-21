<?php
namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Boolean;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
class View extends \Magento\Catalog\Block\Product\View {
    protected $_fishpig;
    protected $_filter;
    private $productId;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \FishPig\WordPress\Model\App\Factory $factory,
        \SuttonSilver\WordpressProductPage\Helper\Filter $filter,
        array $data = []
    )
    {
        $this->_fishpig = $factory;
        $this->_filter = $filter;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
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
    public function getFilter()
    {
        return $this->_filter;
    }
    public function getProductId(){
        return $this->productId;
    }
    public function setProductId($id)
    {
        $this->productId = $id;
        return $this;
    }

    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }
}
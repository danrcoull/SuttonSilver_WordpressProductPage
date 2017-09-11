<?php
namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View {
    /**
     * @var \FishPig\WordPress\Model\App\Factory
     */
    protected $_fishpig;
    /**
     * @var \SuttonSilver\WordpressProductPage\Helper\Filter
     */
    protected $_filter;
    /**
     * @var
     */
    private $productId;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /***
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \FishPig\WordPress\Model\App\Factory $factory
     * @param \SuttonSilver\WordpressProductPage\Helper\Filter $filter
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
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
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    )
    {
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);

        $this->_fishpig = $factory;
        $this->_filter = $filter;
        $this->urlHelper = $urlHelper;

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

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }


	public function getInductionCollection($id)
	{
		$collection = $this->_fishpig->getFactory('Post')->create()->getCollection()
		                             ->addTermIdFilter($id,'tribe_events_cat')
		                             ->addMetaFieldToSelect('_EventStartDate')
		                             ->addMetaFieldToFilter('_EventStartDate', array('gteq' => date("Y-m-d H:i:s", strtotime('now'))))
		                             ->addMetaFieldToSort('_EventStartDate','asc')
		                             ->setCurPage(1)->setPageSize(4);

		return $collection;
	}

	public function getTaxonomy()
	{
		return  $this->_fishpig->getFactory('Term\Taxonomy')->create();
	}
}
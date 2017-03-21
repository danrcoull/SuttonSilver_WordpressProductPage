<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Shortcode;

class Product extends \FishPig\WordPress\Shortcode\AbstractShortcode
{

    protected $_productRepository;

    /**
     * Constructor
     **/
    public function __construct(
        \FishPig\WordPress\Model\App $app,
        \Magento\Framework\View\Element\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory
    )
    {
        $this->_app = $app;
        $this->_factory = $app->getFactory();
        $this->_layout = $context->getLayout();
        $this->_cache = $context->getCache();
        $this->_cacheState = $context->getCacheState();
        $this->_productRepository = $productRepositoryFactory;
    }

	/**
	 * @return string
	**/
	public function getTag()
	{
		return 'product';
	}
	

	protected function _process()
	{
        $value = $this->getValue();
		if (($shortcodes = $this->_getShortcodesByTag($this->getTag())) !== false) {
			foreach($shortcodes as $it => $shortcode) {
				$params = $shortcode->getParams();

				if (!$params->getSkus()) {
					return $this;
				}

				if (($skus = trim($params->getSkus(), ',')) !== '') {
                  	$products = array();
                    $skus =   str_replace(array('&#8217;', '&#8242;'), '', utf8_encode($skus));
                    $repo = $this->_productRepository->create();
					foreach(explode(',', $skus) as $sku) {
                        $products[] = $repo->get($sku);
					}
				}



				$html = $this->_layout->createBlock('\Magento\Framework\View\Element\Template')
					->setTemplate('SuttonSilver_WordpressProductPage::shortcode/product.phtml')
					->addData($params->getData())
					->setObject($this->getObject())
                    ->setProducts($products)
					->toHtml();

                $this->setValue(str_replace($shortcode['html'], $html, $value));
			}
		}
		
		return $this;
	}
}

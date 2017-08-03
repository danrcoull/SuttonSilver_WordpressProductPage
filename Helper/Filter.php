<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Helper;

class Filter extends \FishPig\WordPress\Helper\Filter
{
    protected $_config;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\FishPig\WordPress\Model\App $app,
		\FishPig\WordPress\Model\Config $config
	)
	{
		parent::__construct($context, $app, $config);

		$this->_app = $app;
		$this->_config = $config;
	}

	public function process($string, $object = null)
	{
		if ($shortcodes = $this->_config->getShortcodes()) {
			foreach($shortcodes as $alias => $class) {
				$string = (string)\Magento\Framework\App\ObjectManager::getInstance()
					->get($class)
					->setObject($object)
					->setValue($string)
					->process();
			}
		}

		return $string;
	}

	public function clean($content)
    {
        $sanatize = str_replace(array('<br />'), '', $content);

        // Specify configuration
        $config = array(
            'indent' => true,
            'show-body-only' => true,
            'new-blocklevel-tags' => 'li',
            'new-inline-tags' => 'li',
            'new-empty-tags' => 'li',
            'wrap' => 200);

        // Tidy
        $tidy = new \tidy;
        $tidy->parseString($sanatize, $config, 'utf8');
        $tidy->cleanRepair();

        return $tidy;
    }
	

}

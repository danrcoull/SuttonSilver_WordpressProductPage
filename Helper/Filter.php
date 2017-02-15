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
	

}

<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Shortcode;

class TribeEvents extends \FishPig\WordPress\Shortcode\AbstractShortcode
{

	/**
	 * @return string
	**/
	public function getTag()
	{
		return 'tribe_events';
	}

	public function requiresAssetInjection()
	{
		return true;
	}

	protected function _process()
	{
        $value = $this->getValue();
		if (($shortcodes = $this->_getShortcodesByTag($this->getTag())) !== false) {
			foreach($shortcodes as $it => $shortcode) {
                $atts = $shortcode->getParams();

                static $deployed = false;

                if ( tribe_is_event_query() || $deployed ) {
                    return '';
                }

                $shortcode = new \Tribe__Events__Pro__Shortcodes__Tribe_Events( $atts );
                $deployed  = true;

                $output = $shortcode->output();

                $this->setValue(str_replace($shortcode['html'], $output, $value));
			}
		}
		
		return $this;
	}
}

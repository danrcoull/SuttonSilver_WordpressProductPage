<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Shortcode;

class Caldera extends \FishPig\WordPress\Shortcode\AbstractShortcode
{

    protected $_productRepository;


	/**
	 * @return string
	**/
	public function getTag()
	{
		return 'caldera_form';
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

                if( ! empty( $atts[ 'ID' ] ) && empty( $atts[ 'id' ] )){
                    $atts[ 'id' ] = $atts[ 'ID' ];
                }
                if ( ! isset( $atts[ 'id' ] ) ) {
                    return;
                }

                if ( $shortcode === 'caldera_form_modal' || ( ! empty( $atts[ 'modal' ] ) && $atts[ 'modal' ] ) ) {
                    return \Caldera_Forms_Render_Modals::modal_form( $atts, $shortcode['html'] );
                }

                $html = \Caldera_Forms::render_form( $atts );

                $this->setValue(str_replace($shortcode['html'], $html, $value));
			}
		}
		
		return $this;
	}
}

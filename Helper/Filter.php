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

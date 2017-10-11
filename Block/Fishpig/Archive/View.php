<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Block\Fishig\Archive;


class View extends \FishPig\WordPress\Block\Archive\View
{

	protected function _getPostCollection()
	{

	    $postType = $_GET['post_type'];
		return parent::_getPostCollection()
			->addArchiveDateFilter($this->getArchiveId(), $this->getArchive()->getIsDaily())
			->addPostTypeFilter(($postType) ?: 'post');
	}

}
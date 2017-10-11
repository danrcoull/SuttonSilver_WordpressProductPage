<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPages\Block\Fishig\Archive;

use \FishPig\WordPress\Model\Archive;

class View extends \FishPig\WordPress\Block\Archive\View
{
    protected  $_registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FishPig\WordPress\Block\Context $wpContext,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_registry = $registry;

    }

	protected function _getPostCollection()
	{

        if($postTypeCode = $this->_registry->registry('wordpress_post_type')) {
            $postTypeCode = $postTypeCode->getPostType();
        }elseif($postTypeCode = $this->_registry->registry('wordpress_post')) {
            $postTypeCode = $postTypeCode->getPostType();
        }

		return parent::_getPostCollection()
			->addArchiveDateFilter($this->getArchiveId(), $this->getArchive()->getIsDaily())
			->addPostTypeFilter($postTypeCode);
	}

}

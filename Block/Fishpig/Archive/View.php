<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Block\Fishpig\Archive;

class View extends \FishPig\WordPress\Block\Post\PostList\Wrapper\AbstractWrapper
{
    /**
     * @return \FishPig\WordPress\Model\Archive
     **/
    public function getEntity()
    {
        return $this->getArchive();
    }

    /**
     * Caches and returns the archive model
     *
     * @return Fishpig_Wordpress_Model_Archive
     */
    public function getArchive()
    {
        if (!$this->hasArchive()) {
            $this->setArchive($this->_registry->registry('wordpress_archive'));
        }

        return $this->_getData('archive');
    }

    /**
     * Retrieve the Archive ID
     *
     * @return false|int
     */
    public function getArchiveId()
    {
        if ($archive = $this->getArchive()) {
            return $archive->getId();
        }

        return false;
    }

    /**
     * Generates and returns the collection of posts
     *
     * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
     */
    protected function _getPostCollection()
    {
        $postType = $_GET['post_type'] ?: "post";
        return parent::_getPostCollection()
            ->addArchiveDateFilter($this->getArchiveId(), $this->getArchive()->getIsDaily())
            ->addPostTypeFilter($postType);
    }

    /**
     * Split a date by spaces and translate
     *
     * @param string $date
     * @param string $splitter = ' '
     * @return string
     */
    public function translateDate($date, $splitter = ' ')
    {
        return $this->_viewHelper->translateDate($date, $splitter);
    }
}
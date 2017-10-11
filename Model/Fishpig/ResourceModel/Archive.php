<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace SuttonSilver\WordpressProductPage\Model\Fishpig\ResourceModel;

class Archive extends \FishPig\WordPress\Model\ResourceModel\Archive
{

    protected  $_registry;
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \FishPig\WordPress\Model\Context $wpContext,
        \Magento\Framework\Registry $registry,

        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {


        $this->_registry = $registry;
        parent::__construct($context,$wpContext);
    }

	/**
	 * Set the table and primary key
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('wordpress_post', 'ID');
	}
	
	public function getDatesForWidget()
	{
	    if($postTypeCode = $this->_registry->registry('wordpress_post_type')) {
            $postTypeCode = $postTypeCode->getPostType();
        }elseif($postTypeCode = $this->_registry->registry('wordpress_post')) {
            $postTypeCode = $postTypeCode->getPostType();
        }

		return $this->getConnection()
			->fetchAll(
                "SELECT DISTINCT   COUNT( id ) as post_count,  CONCAT(SUBSTRING(post_date, 1, 4), '/', SUBSTRING(post_date, 6, 2)) AS archive_date 
                  FROM " . $this->getMainTable() . "
                  WHERE post_status = 'publish' and post_date <= now( ) and post_type='".$postTypeCode."' 
                  GROUP BY archive_date ORDER BY post_date DESC"
			);


	}
}

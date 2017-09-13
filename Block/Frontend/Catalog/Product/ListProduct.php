<?php
namespace SuttonSilver\WordpressProductPage\Block\Frontend\Catalog\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ListProduct extends  \Magento\Catalog\Block\Product\ListProduct
{

	/**
	 * @var \FishPig\WordPress\Model\App\Factory
	 */
	protected $_fishpig;
	/**
	 * @var \SuttonSilver\WordpressProductPage\Helper\Filter
	 */
	protected $_filter;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Catalog\Model\Layer\Resolver $layerResolver,
		CategoryRepositoryInterface $categoryRepository,
		\Magento\Framework\Url\Helper\Data $urlHelper,
		\FishPig\WordPress\Model\App\Factory $factory,
		\SuttonSilver\WordpressProductPage\Helper\Filter $filter,
		array $data = []
	) {
		parent::__construct(
			$context,
			$postDataHelper,
			$layerResolver,
			$categoryRepository,
			$urlHelper,
			$data
		);

		$this->_fishpig = $factory;
        $this->_filter = $filter;

	}

	public function getPostById($id)
	{
		$post = $this->_fishpig->getFactory('Post')->create()->load($id);
		return $post;
	}
}
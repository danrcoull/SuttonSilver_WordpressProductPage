<?php


namespace SuttonSilver\CustomCheckout\Observer;
use Psr\Log\LoggerInterface;

class CheckoutCartAddAfter implements \Magento\Framework\Event\ObserverInterface
{


	/**
	 * @var \Magento\Quote\Model\Quote
	 */
	protected $quote;
	protected $cartItemRepository;
	protected $cartItem;
	protected $productRepository;
	protected $formKey;
	protected $logger;
	protected $productConfig;



	/**
	 * Plugin constructor.
	 *
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 */
	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Catalog\Helper\Product\Configuration $productConfig,
		LoggerInterface $logger
	) {
		$this->quote = $checkoutSession->getQuote();
		$this->productRepository =$productRepository;
		$this->formKey =$formKey;
		$this->logger =$logger;
		$this->productConfig = $productConfig;
	}
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
	    var_dump($observer->getEvent()->getQuoteItem());
	    var_dump($this->getProductOptions($observer->getEvent()->getQuoteItem()));
	    die;
    }

	public function getProductOptions($product)
	{
		/* @var $helper \Magento\Catalog\Helper\Product\Configuration */
		$helper = $this->productConfig;
		return $helper->getCustomOptions($product);
	}
}
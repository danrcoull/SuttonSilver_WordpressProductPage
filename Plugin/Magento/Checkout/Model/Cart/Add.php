<?php
namespace SuttonSilver\WordpressProductPage\Plugin\Magento\Checkout\Model\Cart;
use Psr\Log\LoggerInterface;
class Add
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

    protected $ids;

    protected $skus = [
        'INDDAY3',
        'INDDAY6',
        'L6GFTIND'
    ];

    protected $seasons = ["Winter","Spring","Summer","Autumn"];

    private $gtfd = false;

    /**
     * beforeAddProduct
     *
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
	public function beforeAddProduct(
		\Magento\Checkout\Model\Cart $subject,
		$productInfo,
		$requestInfo = null
	) {

        $induction = [];
        $revision = [];
        $this->ids = $subject->getQuoteProductIds();
        $this->logger->addInfo(print_r($requestInfo['options'], true));
        if(isset($requestInfo['options']) && !in_array($productInfo->getSku(),$this->skus))
        {
            foreach($requestInfo['options'] as $key => $val) {

                $title = $productInfo->getOptionById($key)->getTitle();

                if (stripos(strtolower($title), 'induction') !== false) {
	                if ( stripos( strtolower( $title ), 'day' ) !== false ) {
		                if ( isset( $val[0] ) ) {
			                $this->logger->addInfo(print_r($val[0],true));
			                $yesNoValue = $productInfo->getOptionById( $val[0] )->getTitle();
			                $this->logger->addInfo(print_r($yesNoValue,true));

			                if ( $yesNoValue === 'Yes, GFTD' ) {
			                    $this->gtfd = true;
			                }
		                }
	                }
	                if ( stripos( strtolower( $title ), 'date' ) !== false ) {
		                $induction['date'] = $val;
		                unset( $requestInfo['options'][ $key ] );
	                } elseif ( stripos( strtolower( $title ), 'location' ) !== false ) {
		                $induction['location'] = $val;
		                unset( $requestInfo['options'][ $key ] );
	                }
                }

                if (stripos(strtolower($title), 'revision') !== false) {
                    if (stripos(strtolower($title), 'date') !== false) {
                        $revision['date'] = $val;
                        unset($requestInfo['options'][$key]);
                    } elseif (stripos(strtolower($title), 'location') !== false) {
                        $revision['location'] = $val;
                        unset($requestInfo['options'][$key]);
                    }
                }

            }

            if((isset($induction['date']) && $induction['date'] != '') &&
                (isset($induction['location']) && $induction['location'] != '')) {

                $this->addInductionToCart($productInfo, $subject, $induction);
            }

            if((isset($revision['date']) && $revision['date'] != '') &&
                (isset($revision['location']) && $revision['location'] != '')) {
                $this->addRevisionToCart($productInfo, $subject);
            }
        }

        return array($productInfo, $requestInfo);
    }


    public function addRevisionToCart($product)
    {
        $season = $this->seasons[(int)((date("n") %12)/3)];

    }


    public function addInductionToCart($product, $subject, $induction)
    {
        $inductionType = $product->getAttributeText('induction_type');

        switch (trim($inductionType)) {
            case 'Level 3 induction':
                $p = $this->productRepository->get($this->skus[0]);
                break;
            case 'Level 6 induction':
            	if($this->gtfd == false) {
		            $p = $this->productRepository->get( $this->skus[1] );
	            }else{
		            $p =  $this->productRepository->get($this->skus[2]);
	            }
                break;
        }


        try {
            $poptions = $p->getOptions();

            $options = [];
            foreach($poptions as $key => $val) {
                $title=  $val->getTitle();
                if (stripos(strtolower($title), 'induction') !== false) {
                    if (stripos(strtolower($title), 'date') !== false) {
                        $options[$val->getId()] = $induction['date'];
                    } elseif (stripos(strtolower($title), 'location') !== false) {
                        $options[$val->getId()] = $induction['location'];
                    }
                }
            }


            if(!in_array($p->getId(),$this->ids)) {
                    $subject->addProduct($p, ['form_key' => $this->formKey->getFormKey(), 'qty' => 1, 'options' => $options]);
            }else{
                $item = $subject->getItems()->addFieldToFilter('product_id', $p->getId())->getFirstItem();
                $data = [$item->getId()];

                array_push($data[$item->getId()]['options'],$options);
                $subject->updateItems($data);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            //die;
        }

    }
}
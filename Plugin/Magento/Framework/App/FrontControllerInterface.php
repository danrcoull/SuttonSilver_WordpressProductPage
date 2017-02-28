<?php


namespace SuttonSilver\WordpressProductPage\Plugin\Magento\Framework\App;

class FrontControllerInterface
{

    protected $urlInterface;
    protected $storeManager;
    protected  $resultFactory;
    protected  $state;

    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->state = $state;
    }

    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $url = $this->urlInterface->getCurrentUrl();
        if(
            strpos($url, "product") &&
            $this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND
        )
        {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl(str_replace('/products/wordpress-','',$url));
            return $resultRedirect;
        }else {
            $result = $proceed($request);
        }

        return $result;
    }
}

<?php

namespace Gpf\NewsLetterPopup\Plugin;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface as StoreConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Newsletterpopup\Controller\Index\Block;

class CheckRestictedSitePlugin
{
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * Block constructor.
     * @param CustomerSession $customerSession
     * @param StoreConfig $storeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CustomerSession $customerSession, StoreConfig $storeConfig, StoreManagerInterface $storeManager)
    {
        $this->customerSession = $customerSession;
        $this->storeConfig = $storeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @param $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->storeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return int
     */
    private function getStatusCode()
    {
        if ($this->getConfigValue('general/restriction/is_active')
            && !$this->customerSession->isLoggedIn()) {
            return 302;
        }
        return 200;
    }

    public function afterExecute(Block $subject, $result)
    {
        return $subject->getResponse()->clearHeader('Location')->setHttpResponseCode($this->getStatusCode());//$this->getStatusCode()
    }
}

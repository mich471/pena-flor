<?php
namespace WeltPixel\Multistore\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    const XML_PATH_MULTISTORE_ENABLED = 'weltpixel_multistore/general/enable';
    const XML_PATH_MULTISTORE_ONEROW_DESKTOP = 'weltpixel_multistore/general/one_row';
    const XML_PATH_MULTISTORE_ONEROW_MOBILE = 'weltpixel_multistore/general/one_row_mobile';

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add Custom QuickCart layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        $isEnabled = $this->scopeConfig->getValue(self::XML_PATH_MULTISTORE_ENABLED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $showInOneRowDesktop = $this->scopeConfig->getValue(self::XML_PATH_MULTISTORE_ONEROW_DESKTOP,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $showInOneRowMobile = $this->scopeConfig->getValue(self::XML_PATH_MULTISTORE_ONEROW_MOBILE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled && ($showInOneRowDesktop || $showInOneRowMobile)) {
            $layout->getUpdate()->addHandle('weltpixel_multistore');
        }
        if ($isEnabled && $showInOneRowDesktop) {
            $layout->getUpdate()->addHandle('weltpixel_multistore_onerow');
        }
        if ($isEnabled && $showInOneRowMobile) {
            $layout->getUpdate()->addHandle('weltpixel_multistore_onerow_mobile');
        } else if ($isEnabled && !$showInOneRowMobile) {
            $layout->getUpdate()->addHandle('weltpixel_multistore_dropdown_mobile');
        }

        return $this;
    }
}

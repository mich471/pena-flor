<?php

namespace WeltPixel\SpeedOptimization\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddCustomLayoutsObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    const XML_PATH_SPEEDOPTIMIZATION_REMOVE_PRINTCSS = 'weltpixel_speedoptimization/css_optimization/remove_printcss';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \WeltPixel\DesignElements\Observer\AddFrontendOptionsObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        $removePrintCss = $this->scopeConfig->getValue(self::XML_PATH_SPEEDOPTIMIZATION_REMOVE_PRINTCSS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($removePrintCss) {
            $layout->getUpdate()->addHandle('weltpixel_speedoptimization_remove_printcss');
        }

        return $this;
    }
}

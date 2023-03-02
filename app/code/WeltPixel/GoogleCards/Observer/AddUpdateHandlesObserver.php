<?php

namespace WeltPixel\GoogleCards\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{

    const XML_PATH_GOOGLECARDS_ENABLE_FACEBOOK_GRAPH = 'weltpixel_google_cards/facebook_opengraph/enable';
    const XML_PATH_GOOGLECARDS_ENABLE_GOOGLE_CARDS = 'weltpixel_google_cards/general/enable';
    const XML_PATH_GOOGLECARDS_BREADCRUMBS_ENABLED = 'weltpixel_google_cards/general/breadcrumbs';
    const XML_PATH_GOOGLECARDS_BREADCRUMBS_TYPE = 'weltpixel_google_cards/general/breadcrumbs_type';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $resgistry
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $resgistry
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $resgistry;
    }

    /**
     * Add New Layout handle
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

        $enableFacebookGraph = $this->scopeConfig->getValue(self::XML_PATH_GOOGLECARDS_ENABLE_FACEBOOK_GRAPH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableGoogleCards = $this->scopeConfig->getValue(self::XML_PATH_GOOGLECARDS_ENABLE_GOOGLE_CARDS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableBreadCrumbs = $this->scopeConfig->getValue(self::XML_PATH_GOOGLECARDS_BREADCRUMBS_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $breadCrumbsType = $this->scopeConfig->getValue(self::XML_PATH_GOOGLECARDS_BREADCRUMBS_TYPE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($enableFacebookGraph) {
            $layout->getUpdate()->addHandle('weltpixel_googlecards_remove_opengraph');
        }

        if ($enableGoogleCards) {
            $layout->getUpdate()->addHandle('weltpixel_googlecards_remove_schema');
        }

        $currentProduct = $this->registry->registry('current_product');

        if ($currentProduct && $enableBreadCrumbs && ($breadCrumbsType == \WeltPixel\GoogleCards\Model\Config\Source\BreadcrumbsType::BREADCRUMB_FULL)) {
            $layout->getUpdate()->addHandle('weltpixel_googlecards_product_breadcrumb_full');
        }

        return $this;
    }
}

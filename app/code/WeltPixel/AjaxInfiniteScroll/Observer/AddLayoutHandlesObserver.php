<?php
namespace WeltPixel\AjaxInfiniteScroll\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class AddLayoutHandlesObserver implements ObserverInterface
{
    const AJAX_INFINITESCROLL_ENABLED = 'weltpixel_infinite_scroll/general/ajax_catalog';
    const AJAX_INFINITESCROLL_MODE = 'weltpixel_infinite_scroll/general/ajax_catalog_mode';
    const AJAX_INFINITESCROLL_ENABLED_ON_CATEGORY = 'weltpixel_infinite_scroll/general/ajax_catalog_category';
    const AJAX_INFINITESCROLL_ENABLED_ON_SEARCH = 'weltpixel_infinite_scroll/general/ajax_catalog_search';
    const AJAX_INFINITESCROLL_ENABLED_ON_ADVANCEDSEARCH = 'weltpixel_infinite_scroll/general/ajax_catalog_advanced';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AddLayoutHandlesObserver constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $isEnabled = $this->scopeConfig->getValue(self::AJAX_INFINITESCROLL_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
        $ajaxMode = $this->scopeConfig->getValue(self::AJAX_INFINITESCROLL_MODE, ScopeInterface::SCOPE_STORE, $storeId);
        $enabledOnCategory = $this->scopeConfig->getValue(self::AJAX_INFINITESCROLL_ENABLED_ON_CATEGORY, ScopeInterface::SCOPE_STORE, $storeId);
        $enabledOnSearch = $this->scopeConfig->getValue(self::AJAX_INFINITESCROLL_ENABLED_ON_SEARCH, ScopeInterface::SCOPE_STORE, $storeId);
        $enabledOnAdvancedSearch = $this->scopeConfig->getValue(self::AJAX_INFINITESCROLL_ENABLED_ON_ADVANCEDSEARCH, ScopeInterface::SCOPE_STORE, $storeId);

        $layout = $observer->getData('layout');
        $currentHandles = $layout->getUpdate()->getHandles();
        $addNewHandle = false;

        if ($isEnabled && ( $ajaxMode == 'infinite_scroll' ) ) {
            if ($enabledOnSearch && in_array('catalogsearch_result_index', $currentHandles)) {
                $addNewHandle = true;
            }
            if ($enabledOnAdvancedSearch && in_array('catalogsearch_advanced_result', $currentHandles)) {
                $addNewHandle = true;
            }
            if ($enabledOnCategory && array_intersect($currentHandles, [
                    'catalog_category_view',
                    'catalog_category_view_type_layered',
                ])) {
                $addNewHandle = true;
            }
        }

        if ($addNewHandle) {
            $layout->getUpdate()->addHandle('weltpixel_ajaxinfinitescroll');
        }

        return $this;
    }
}

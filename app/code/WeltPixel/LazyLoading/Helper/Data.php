<?php

namespace WeltPixel\LazyLoading\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $_currentStore;

    /**
     * @var array
     */
    protected $_lazyLoadOptions;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\Data\StoreInterface $currentStore
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\Data\StoreInterface $currentStore,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($context);

        $this->_storeManager = $storeManager;
        $this->_currentStore = $currentStore;
        $this->_assetRepo = $assetRepo;
        $this->_layout = $layout;
        $this->_lazyLoadOptions = $this->scopeConfig->getValue('weltpixel_lazy_loading', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['general']['enable'];
        }
    }

    /**
     * @return string
     */
    public function getImageLoader() {
        if ($this->startLoadingPlaceholder()) {
            $customImgLoader = $this->getIconUrl();
            if (!empty($customImgLoader)) {
                return $customImgLoader;
            }
        }

        return $this->_assetRepo->getUrlWithParams('WeltPixel_LazyLoading::images/Loader.gif', []);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getNegativeMargin($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/negative_margin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['advanced']['negative_margin'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function startLoadingEarly($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/loading_early', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['advanced']['loading_early'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getEffectSpeed($storeId = 0) {
        if ($storeId) {
            return (int)$this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/effect_speed', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return (int)$this->_lazyLoadOptions['advanced']['effect_speed'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function startLoadingPlaceholder($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/loading_placeholder', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['advanced']['loading_placeholder'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed|string
     */
    public function getPlaceholderWidth($storeId = 0) {
        $imgWidth = null;
        if ($storeId) {
            $imgWidth = (int) $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/placeholder_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $imgWidth = (int) $this->_lazyLoadOptions['advanced']['placeholder_width'];
        }

        return $imgWidth && is_integer($imgWidth) ? $imgWidth . 'px' : 'auto';
    }

    /**
     * @param $storeId
     * @return string
     */
    private function getIconUrl($storeId = 0) {
        $image = $this->getLoadingIcon($storeId);
        if ($image) {
            $imagePath = 'weltpixel/lazyloading/logo/' . $image;
            $imageUrl = $this->_currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            return $imageUrl . $imagePath;
        }

        return '';
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    private function getLoadingIcon($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_lazy_loading/advanced/loading_icon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_lazyLoadOptions['advanced']['loading_icon'];
        }
    }

    /**
     * Checks if image src should be changed with lazyload preloader
     * @return boolean
     */
    public function isRequestAjax() {
        return $this->_request->isAjax() || $this->_verifyIfLazyLoadShouldBeIgnored();
    }

    /**
     * @return array
     */
    public function getIgnoreHandles()
    {
        return [
            'wishlist_email_items'
        ];
    }

    /**
     * @return bool
     */
    protected function _verifyIfLazyLoadShouldBeIgnored() {
        $layoutHandlesUsed = $this->_layout->getUpdate()->getHandles();
        $ignoredHandles = $this->getIgnoreHandles();
        if (count(array_intersect($layoutHandlesUsed, $ignoredHandles))) {
            return true;
        }

        return false;
    }
}

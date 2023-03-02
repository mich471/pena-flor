<?php

namespace WeltPixel\AdvancedWishlist\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_wishlistOptions;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_wishlistOptions = $this->scopeConfig->getValue('weltpixel_advancedwishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isMultiWishlistEnabled($websiteId = null) {
        if (!$websiteId) {
            return $this->scopeConfig->getValue('weltpixel_advancedwishlist/general/enable_multiwishlist', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $websiteId);
        }
        return trim($this->_wishlistOptions['general']['enable_multiwishlist']);
    }

    /**
     * @return boolean
     */
    public function isAjaxWishlistEnabled() {
        return $this->_wishlistOptions['general']['enable_ajaxwishlist'];
    }

    /**
     * @return boolean
     */
    public function isShareWishlistEnabled() {
        return $this->_wishlistOptions['general']['enable_sharewishlist'];
    }

    /**
     * @return string
     */
    public function getShareJavascript() {
        return trim($this->_wishlistOptions['general']['sharewishlist_js']);
    }

    /**
     * @return boolean
     */
    public function isPriceAlertEnabled() {
        return $this->_wishlistOptions['general']['enable_pricealert'];
    }

    /**
     * @return boolean
     */
    public function isPublicWishlistEnabled() {
        return $this->isShareWishlistEnabled() && $this->_wishlistOptions['general']['enable_publicwishlist'];
    }
}

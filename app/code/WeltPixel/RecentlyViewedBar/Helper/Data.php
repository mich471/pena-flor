<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_RecentlyViewedBar
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */


namespace WeltPixel\RecentlyViewedBar\Helper;

/**
 * Class Data
 * @package WeltPixel\RecentlyViewedBar\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->_httpContext = $httpContext;

    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDisplayOn($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/display_on', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getShowAttributes($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/show_attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getShowButtons($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/show_buttons', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getEnableCms($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/enable_cms', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool|mixed
     */
    public function getEnableSlIntegration($storeId = null)
    {
        if ($this->isSlEnabled() && class_exists('WeltPixel\SocialLogin\Block\Widget\Login')) { // first check if Social Login extension is enabled
            return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/enable_sl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }

        return false;
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCmsBlock($storeId = null)
    {
        if(!$this->getEnableCms()) {
            return false;
        }

        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/cms_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonTextColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonLabel($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getItemLimit($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/item_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function getIsPriceEnabled() {
        $attrStr = $this->getShowAttributes();
        $pos = strpos($attrStr, '3');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getIsNameEnabled() {
        $attrStr = $this->getShowAttributes();
        $pos = strpos($attrStr, '2');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getIsImageEnabled() {
        $attrStr = $this->getShowAttributes();
        $pos = strpos($attrStr, '1');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getIsAddtocartEnabled() {
        $attrStr = $this->getShowButtons();
        $pos = strpos($attrStr, '1');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getIsAddtocompareEnabled() {
        $attrStr = $this->getShowButtons();
        $pos = strpos($attrStr, '2');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function getIsAddtowishlistEnabled() {
        $attrStr = $this->getShowButtons();
        $pos = strpos($attrStr, '3');
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }

    public function isCustomerLoggedIn() {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function isSlEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_sociallogin/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonPosition($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonLabelIcon($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_label_icon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonTooltipText($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_tooltip_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonMarginLeft($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_margin_left', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getButtonMarginRight($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_RecentlyViewedBar/general/button_margin_right', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}

<?php

namespace WeltPixel\UserProfile\Helper;

/**
 * Class Data
 * @package WeltPixel\UserProfile\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_userProfileOptions;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
        $this->_userProfileOptions = $this->scopeConfig->getValue('weltpixel_userprofile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_userProfileOptions['general']['enable'];
    }

    /**
     * @return bool
     */
    public function isInlineEditEnabled()
    {
        return $this->_userProfileOptions['general']['enable_inline_edit'];
    }

    /**
     * @return bool
     */
    public function isReviewsProfileEnabled()
    {
        return $this->_userProfileOptions['general']['enable_on_review'];
    }

    /**
     * @return bool
     */
    public function isWishlistDisplayEnabled()
    {
        return $this->_userProfileOptions['general']['enable_wishlist_display'];
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isFieldEnabled($fieldName)
    {
        return $this->_userProfileOptions['general'][$fieldName . '_enable'];
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isFieldRequired($fieldName)
    {
        return $this->_userProfileOptions['general'][$fieldName . '_required'];
    }

    public function getEditProfileLink()
    {
        if (!$this->isInlineEditEnabled()) {
            return 'profile/account';
        }

        return 'profile/user';
    }

    /**
     * @return string
     */
    public function getViewTemplate()
    {
        if (!$this->isInlineEditEnabled()) {
            return 'WeltPixel_UserProfile::view.phtml';
        }

        return 'WeltPixel_UserProfile::view_inline_edit.phtml';
    }
}

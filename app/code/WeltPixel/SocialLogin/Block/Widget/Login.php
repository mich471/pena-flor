<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block\Widget;

use WeltPixel\SocialLogin\Block\ButtonDataProvider;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Login
 * @package WeltPixel\SocialLogin\Block\Widget
 */
class Login extends ButtonDataProvider implements BlockInterface
{

    const BUTTONS_NUMBER_LIMIT = 3;

    protected $_template = "widget/login.phtml";

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return (bool)!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerUrl->getLoginPostUrl();
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_customerUrl->getForgotPasswordUrl();
    }

    /**
     * @return string
     */
    public function getCustomerLoginUrl() {
        return $this->_customerUrl->getLoginUrl();
    }

    /**
     * @return bool
     */
    public function getLimit() {
        return self::BUTTONS_NUMBER_LIMIT;
    }
}
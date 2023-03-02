<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Plugin;

use Magento\Customer\Model\Context;

/**
 * Class CreateAccountLink
 * @package WeltPixel\SocialLogin\Plugin
 */
class CreateAccountLink
{
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $_wpHelper;

    /**
     * CreateAccountLink constructor.
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \WeltPixel\SocialLogin\Helper\Data $wpHelper
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \WeltPixel\SocialLogin\Helper\Data $wpHelper
    )
    {
        $this->httpContext = $httpContext;
        $this->_wpHelper = $wpHelper;
    }

    /**
     * @param \Magento\Customer\Block\Account\RegisterLink $subject
     * @param $result
     * @return string
     */
    public function afterGetHref(\Magento\Customer\Block\Account\RegisterLink $subject, $result)
    {
        if($this->_wpHelper->getConfig('weltpixel_sociallogin/general/enabled') &&
           $this->_wpHelper->getConfig('weltpixel_sociallogin/general/popup')){
            if (!$this->isLoggedIn()) {
                $result = '#';
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
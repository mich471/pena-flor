<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Plugin;


class LoginPostPlugin
{
    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $_wpHelper;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * LoginPostPlugin constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \WeltPixel\SocialLogin\Helper\Data $wpHelper
     */
    public function __construct(
        \WeltPixel\SocialLogin\Helper\Data $wpHelper,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    )
    {
        $this->_wpHelper = $wpHelper;
        $this->redirect = $redirect;
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result)
    {
        if(!$this->_wpHelper->getConfig('weltpixel_sociallogin/general/enabled')){
            return $result;
        }
        $redirectUrl = $this->redirect->getRefererUrl();
        if ($result instanceof  \Magento\Framework\Controller\Result\Forward) {
            return $result;
        } else {
            $result->setPath($redirectUrl);
        }
        return $result;
    }

}
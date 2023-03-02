<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddSignedOutHandleObserver
 * @package WeltPixel\SocialLogin\Observer
 */
class AddSignedOutHandleObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $_wpHelper;

    /**
     * AddSignedOutHandleObserver constructor.
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession,
        \WeltPixel\SocialLogin\Helper\Data $wpHelper
    )
    {
        $this->customerSession = $customerSession;
        $this->_wpHelper = $wpHelper;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();

        if (!$this->customerSession->isLoggedIn() && $this->_wpHelper->getConfig('weltpixel_sociallogin/general/enabled') && $this->_wpHelper->getConfig('weltpixel_sociallogin/general/popup')) {
            $layout->getUpdate()->addHandle('ajaxlogin_customer_signed_out');
        }
    }
}

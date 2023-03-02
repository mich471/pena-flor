<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use WeltPixel\SocialLogin\Helper\Data as SlHelper;
use WeltPixel\SocialLogin\Model\SocialloginFactory;
use WeltPixel\SocialLogin\Model\OrderUserFactory;

/**
 * Class OrderUser
 * @package WeltPixel\SocialLogin\Observer
 */
class OrderUser implements ObserverInterface
{
    /**
     * @var SlHelper
     */
    protected $_slHelper;
    /**
     * @var Session
     */
    protected $_session;
    /**
     * @var SocialloginFactory
     */
    protected $socialLoginFactory;
    /**
     * @var OrderUserFactory
     */
    protected $orderUserFactory;

    /**
     * LoginObserver constructor.
     * @param SlHelper $slHelper
     * @param Session $customerSession
     */
    public function __construct(
        SlHelper $slHelper,
        Session $customerSession,
        SocialloginFactory $socialLoginFactory,
        OrderUserFactory $orderUserFactory
    ) {
        $this->_slHelper = $slHelper;
        $this->_session = $customerSession;
        $this->socialLoginFactory = $socialLoginFactory;
        $this->orderUserFactory = $orderUserFactory;
    }

    public function execute(Observer $observer)
    {
        if(!$this->_slHelper->isEnabled()) {
            return;
        }

        $socialLoginSession = $this->_session->getData('sociallogin');
        if(!is_array($socialLoginSession)) {
            return;
        }

        $orderIds = $observer->getEvent()->getOrderIds();
        $orderId = (isset($orderIds[0])) ? $orderIds[0] : false;
        $customerId = $this->_session->getCustomerId();

        if(!$customerId || !$orderId) {
            return;
        }
        $type = (isset($socialLoginSession['provider'])) ?  $socialLoginSession['provider'] : 'default';
        $userId = $this->socialLoginFactory->create()->getUserIdByParams($customerId, $type);
        if(!$userId) {
            return;
        }
        $this->orderUserFactory->create()->setOrderUser($orderId, $userId, $customerId, $type);


    }
}

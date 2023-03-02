<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Controller\Account;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use WeltPixel\SocialLogin\Helper\Data as SocialHelper;
use WeltPixel\SocialLogin\Model\Sociallogin;

/**
 * Class Email
 * @package WeltPixel\SocialLogin\Controller\Account
 */
class Email extends \WeltPixel\SocialLogin\Controller\AbstractAccount
{
    /**
     * @type \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var Customer
     */
    protected $customerFactory;
    /**
     * @var Sociallogin
     */
    protected $model;

    /**
     * Email constructor.
     * @param Context $context
     * @param SocialHelper $slHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param RawFactory $resultRawFactory
     * @param JsonFactory $resultJsonFactory
     * @param CustomerFactory $customerFactory
     * @param Sociallogin $model
     * @param Session $customerSession
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        CustomerFactory $customerFactory,
        Sociallogin $model,
        Session $customerSession,
        \Magento\Framework\View\LayoutInterface $layout
    )
    {
        parent::__construct($context, $customerSession, $slHelper, $storeManager, $resultRawFactory, $layout);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerFactory = $customerFactory;
        $this->model = $model;

    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $isLoggedIn = $this->_getSession()->isLoggedIn();
        if($isLoggedIn){
            return;
        }

        $type = $this->getRequest()->getParam('type', null);
        $this->getRequest()->setParam('type', false);

        if(!$type) {
            return;
        }
        $this->_setType($type);
        $redirectUrl  = $this->getRequest()->getParam('redirect-url', null);

        if (!$type) {
            $this->_forward('noroute');

            return;
        }

        $result = ['success' => false];

        $realEmail = $this->getRequest()->getParam('real-email', null);
        if (!$realEmail) {
            $result['message'] = __('No email address provided.');

            return $resultJson->setData($result);
        }

        $customer = $this->customerFactory->create()
            ->setWebsiteId($this->storeManager->getStore()->getWebsiteId())
            ->loadByEmail($realEmail);
        if ($customer->getId()) {
            $result['message'] = __('Email address already exists');

            return $resultJson->setData($result);
        }

        $userProfile = $this->customerSession->getUserProfile();
        $userProfile['email'] = $realEmail;

        $customer = $this->createCustomerProcess($userProfile);
        $this->refresh($customer);

        $result['success'] = true;
        $result['message'] = __('Customer registration successful.');
        $result['url'] = $redirectUrl ?: $this->slHelper->getRedirectUrl();

        $userProfileData = $this->customerSession->getUserProfileData();
        if ($userProfileData && $customer) {
            $customerId = $customer->getId();
            $this->_eventManager->dispatch(
                'weltpixel_userprofile_create',
                [
                    'customer_id' => $customerId,
                    'profile_data' => $userProfileData
                ]
            );
        }

        return $resultJson->setData($result);
    }

}

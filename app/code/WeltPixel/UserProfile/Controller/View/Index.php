<?php

namespace WeltPixel\UserProfile\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use WeltPixel\UserProfile\Block\ViewProfile as ViewProfileBlock;
use WeltPixel\UserProfile\Helper\Data as ProfileHelper;
use WeltPixel\UserProfile\Model\UserProfileFactory;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Index
 * @package WeltPixel\UserProfile\Controller\View
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var ProfileHelper
     */
    protected $profileHelper;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param ProfileHelper $profileHelper
     * @param UserProfileFactory $userProfileFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ProfileHelper $profileHelper,
        UserProfileFactory $userProfileFactory,
        CustomerSession $customerSession
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->profileHelper = $profileHelper;
        $this->userProfileFactory = $userProfileFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $isModuleEnabled = $this->profileHelper->isEnabled();
        $username = $this->getRequest()->getParam('username');
        if (!$isModuleEnabled) {
            return $this->_redirectBack();
        }

        if ($this->customerSession->isLoggedIn() && !$username) {
            $customerId = $this->customerSession->getCustomer()->getId();

            $userProfile = $this->userProfileFactory->create()->loadByCustomerId($customerId);
            if (!$userProfile->getId()) {
                //$customerEmail = $this->customerSession->getCustomer()->getEmail();
                //$username = strtolower(preg_replace('/[^A-Za-z0-9]/', '_',strstr($customerEmail, '@', true)));
                $username = $customerId . uniqid();
                $userProfile->setUsername($username);
                $userProfile->setCustomerId($customerId);
                $userProfile->save();
            }
            return $this->_redirect('profile/user/' . $userProfile->getUsername());

        } elseif (!$username) {
            return $this->_redirectBack();
        }

        $userProfile = $this->userProfileFactory->create();
        try {
            $userProfile->loadByUsername($username);
        } catch (\Exception $ex) {
            return $this->_redirectBack();
        }

        $resultPage = $this->pageFactory->create();
        /** @var ViewProfileBlock $userProfileBlock */
        $userProfileBlock = $resultPage->getLayout()->getBlock('weltpixel.userprofile.view');

        if ($userProfileBlock) {
            $userProfileBlock->setProfile($userProfile);
            if (!$userProfile->getId()) {
                $userProfileBlock->setTemplate("WeltPixel_UserProfile::view/not_found.phtml");
                $resultPage->getConfig()->getTitle()->set(__('Profile Not Available'));
            } else {
                $resultPage->getConfig()->getTitle()->set(__('Profile Page'));
            }
        }

        return $resultPage;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _redirectBack()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererOrBaseUrl();
        return $resultRedirect;
    }
}

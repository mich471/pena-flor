<?php

namespace WeltPixel\UserProfile\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use WeltPixel\UserProfile\Model\UserProfileFactory;

/**
 * Class Editable
 * @package WeltPixel\UserProfile\Controller\View
 */
class Editable extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * Editable constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UserProfileFactory $userProfileFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        UserProfileFactory $userProfileFactory
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->userProfileFactory = $userProfileFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $profileId = $this->getRequest()->getParam('profile_id');
        $result = [
            'editable' => false
        ];
        if (!$profileId) {
            return $this->prepareResult($result);
        }

        try {
            $userProfile = $this->userProfileFactory->create()->load($profileId);
        } catch (\Exception $ex) {
            return $this->prepareResult($result);
        }

        $loggedInUserId = $this->customerSession->getCustomerId();
        $profileCustomerId = $userProfile->getCustomerId();

        if ($loggedInUserId == $profileCustomerId) {
            $result['editable'] = true;
        }

        return $this->prepareResult($result);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

}

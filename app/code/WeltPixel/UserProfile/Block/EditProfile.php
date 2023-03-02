<?php

namespace WeltPixel\UserProfile\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\UserProfile\Model\UserProfileFactory;
use WeltPixel\UserProfile\Model\UserProfileFields;

/**
 * Class EditProfile
 * @package WeltPixel\UserProfile\Block
 */
class EditProfile extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * EditProfile constructor.
     * @param CustomerSession $customerSession
     * @param UserProfileFields $userProfileFields
     * @param UserProfileFactory $userProfileFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CustomerSession $customerSession,
        UserProfileFields $userProfileFields,
        UserProfileFactory $userProfileFactory,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->userProfileFields = $userProfileFields;
        $this->userProfileFactory = $userProfileFactory;
    }

    /**
     * @return array
     */
    public function getFormElements()
    {
        return $this->userProfileFields->getFieldsOptions();
    }

    /**
     * @return integer
     */
    public function getLoggedInCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * @return \WeltPixel\UserProfile\Model\UserProfile
     */
    public function getUserProfile()
    {
        $userProfile = $this->userProfileFactory->create();
        $customerId = $this->getLoggedInCustomerId();
        $userProfile->loadByCustomerId($customerId);

        return $userProfile;
    }

}

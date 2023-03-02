<?php

namespace WeltPixel\UserProfile\Observer;

use WeltPixel\UserProfile\Model\UserProfileBuilder;
use Magento\Framework\Event\ObserverInterface;


class CreateProfileObserver implements ObserverInterface
{
    /**
     * @var UserProfileBuilder
     */
    protected $userProfileBuilder;

    /**
     * @param UserProfileBuilder $userProfileBuilder
     */
    public function __construct(
        UserProfileBuilder $userProfileBuilder
    )
    {
        $this->userProfileBuilder = $userProfileBuilder;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerId = $observer->getData('customer_id');
        $profileData = $observer->getData('profile_data');

        $this->userProfileBuilder->build($customerId, $profileData);

        return $this;
    }
}
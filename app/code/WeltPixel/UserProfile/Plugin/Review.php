<?php

namespace WeltPixel\UserProfile\Plugin;

use Magento\Framework\Event\ManagerInterface;
use WeltPixel\UserProfile\Helper\Data as UserProfileHelper;
use WeltPixel\UserProfile\Model\UserProfileFactory;

class Review
{
    /**
     * @var UserProfileHelper
     */
    protected $userProfileHelper;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    public function __construct(
        UserProfileHelper $userProfileHelper,
        ManagerInterface $eventManager,
        UserProfileFactory $userProfileFactory
    )
    {
        $this->userProfileHelper = $userProfileHelper;
        $this->eventManager = $eventManager;
        $this->userProfileFactory = $userProfileFactory;
    }

    /**
     * @param \Magento\Review\Model\Review $subject
     * @param $result
     * @return \Magento\Review\Model\Review
     */
    public function afterAfterSave(
        \Magento\Review\Model\Review $subject,
        $result
    )
    {
        $customerId = $subject->getCustomerId();
        $userProfile = $this->userProfileFactory->create()->loadByCustomerId($customerId);

        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $userProfile]);
        return $result;
    }
}
<?php

namespace WeltPixel\UserProfile\Plugin;

use Magento\Framework\Event\ManagerInterface;
use Magento\Wishlist\Model\Wishlist;
use WeltPixel\UserProfile\Helper\Data as UserProfileHelper;
use WeltPixel\UserProfile\Model\UserProfileFactory;

/**
 * Class WishlistModel
 * @package WeltPixel\UserProfile\Plugin
 */
class WishlistModel
{

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var UserProfileHelper
     */
    protected $userProfileHelper;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * WishlistModel constructor.
     * @param ManagerInterface $eventManager
     * @param UserProfileHelper $userProfileHelper
     * @param UserProfileFactory $userProfileFactory
     */
    public function __construct(
        ManagerInterface $eventManager,
        UserProfileHelper $userProfileHelper,
        UserProfileFactory $userProfileFactory
    )
    {
        $this->eventManager = $eventManager;
        $this->userProfileHelper = $userProfileHelper;
        $this->userProfileFactory = $userProfileFactory;
    }

    /**
     * @param Wishlist $subject
     * @param Wishlist $result
     * @return Wishlist
     */
    public function afterAfterSave(
        Wishlist $subject,
        Wishlist $result
    )
    {
        if ($this->userProfileHelper->isWishlistDisplayEnabled()) {
            $customerId = $result->getCustomerId();
            $userProfile = $this->userProfileFactory->create()->loadByCustomerId($customerId);

            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $userProfile]);
        }

        return $result;
    }
}
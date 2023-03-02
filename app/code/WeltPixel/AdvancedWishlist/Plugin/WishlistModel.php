<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use Magento\Framework\Event\ManagerInterface;
use Magento\Wishlist\Model\Wishlist;

class WishlistModel
{

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * WishlistModel constructor.
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ManagerInterface $eventManager
    )
    {
        $this->eventManager = $eventManager;
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
        $this->eventManager->dispatch(
            'wishlist_refresh_pricealert',
            ['wishlist' => $result]
        );

        return $result;
    }
}
<?php

namespace WeltPixel\AdvancedWishlist\Observer;

use Magento\Framework\Event\ObserverInterface;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use \Magento\Store\Model\StoreManagerInterface;
use WeltPixel\AdvancedWishlist\Model\ProductAlertPriceBuilder;
use Magento\Framework\Event\ManagerInterface;

class WishListAddProductObserver implements ObserverInterface
{
    /**
     * @var WishlistHelper
     */
    protected $helper;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductAlertPriceBuilder
     */
    protected $productAlertPriceBuilder;

    /**
     * @param WishlistHelper $helper
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param ProductAlertPriceBuilder $productAlertPriceBuilder
     */
    public function __construct(
        WishlistHelper $helper,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        ProductAlertPriceBuilder $productAlertPriceBuilder
    )
    {
        $this->helper = $helper;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->productAlertPriceBuilder = $productAlertPriceBuilder;
    }

    /**
     * Clear the cache tag for wishlist also when new item added
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isShareWishlistEnabled()) {
            $wishlist = $observer->getData('wishlist');
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $wishlist]);
        }

        if ($this->helper->isPriceAlertEnabled()) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $wishlist = $observer->getData('wishlist');
            $this->productAlertPriceBuilder->refreshProductAlertsForWishlist($wishlist, $websiteId);
        }

        return $this;
    }
}
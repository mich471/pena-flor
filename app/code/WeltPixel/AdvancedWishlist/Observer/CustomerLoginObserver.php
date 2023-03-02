<?php

namespace WeltPixel\AdvancedWishlist\Observer;

use Magento\Framework\Event\ObserverInterface;
use WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider;
use WeltPixel\AdvancedWishlist\Model\ProductAlertPriceBuilder;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use \Magento\Store\Model\StoreManagerInterface;

class CustomerLoginObserver implements ObserverInterface
{
    /**
     * @var ProductAlertPriceBuilder
     */
    protected $productAlertPriceBuilder;

    /**
     * @var WishlistHelper
     */
    protected $wpHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var MultipleWishlistProvider
     */
    protected $multipleWishlistProvider;

    /**
     * @param ProductAlertPriceBuilder $productAlertPriceBuilder
     * @param WishlistHelper $wpHelper
     * @param StoreManagerInterface $storeManager
     * @param MultipleWishlistProvider $multipleWishlistProvider
     */
    public function __construct(
        ProductAlertPriceBuilder $productAlertPriceBuilder,
        WishlistHelper $wpHelper,
        StoreManagerInterface $storeManager,
        MultipleWishlistProvider $multipleWishlistProvider
    )
    {
        $this->productAlertPriceBuilder = $productAlertPriceBuilder;
        $this->wpHelper = $wpHelper;
        $this->storeManager = $storeManager;
        $this->multipleWishlistProvider = $multipleWishlistProvider;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        if (!$this->wpHelper->isPriceAlertEnabled()) {
            return $this;
        }

        $wishlists = $this->multipleWishlistProvider->getWishlists();
        foreach ($wishlists as $wishlist)  {
            $this->productAlertPriceBuilder->refreshProductAlertsForWishlist($wishlist, $websiteId);
        }

        return $this;
    }
}
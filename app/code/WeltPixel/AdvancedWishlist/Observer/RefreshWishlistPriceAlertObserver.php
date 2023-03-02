<?php

namespace WeltPixel\AdvancedWishlist\Observer;

use Magento\Framework\Event\ObserverInterface;
use WeltPixel\AdvancedWishlist\Model\ProductAlertPriceBuilder;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use \Magento\Store\Model\StoreManagerInterface;

class RefreshWishlistPriceAlertObserver implements ObserverInterface
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
     * @param ProductAlertPriceBuilder $productAlertPriceBuilder
     * @param WishlistHelper $wpHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductAlertPriceBuilder $productAlertPriceBuilder,
        WishlistHelper $wpHelper,
        StoreManagerInterface $storeManager
    )
    {
        $this->productAlertPriceBuilder = $productAlertPriceBuilder;
        $this->wpHelper = $wpHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->wpHelper->isPriceAlertEnabled()) {
            return $this;
        }

        $wishlist = $observer->getData('wishlist');
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        $this->productAlertPriceBuilder->refreshProductAlertsForWishlist($wishlist, $websiteId);

        return $this;
    }
}
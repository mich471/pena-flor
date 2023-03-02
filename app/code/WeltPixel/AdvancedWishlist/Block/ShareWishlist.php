<?php

namespace WeltPixel\AdvancedWishlist\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use WeltPixel\AdvancedWishlist\Helper\Data as WpHelper;

class ShareWishlist extends \Magento\Framework\View\Element\Template
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var WpHelper
     */
    protected $wpHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * ShareWishlist constructor.
     * @param WishlistHelper $wishlistHelper
     * @param WpHelper $wpHelper
     * @param CustomerSession $customerSession
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        WishlistHelper $wishlistHelper,
        WpHelper $wpHelper,
        CustomerSession $customerSession,
        Context $context,
        array $data = [])
    {
        $this->wishlistHelper = $wishlistHelper;
        $this->wpHelper = $wpHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        return $this->wishlistHelper->getWishlist();
    }

    /**
     * @return bool
     */
    public function canBeDisplayed()
    {
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$this->wpHelper->isMultiWishlistEnabled()) {
            $wishlistId = $this->getWishlist()->getId();
        }
        $wishlistShareDisabled = $this->getWishlist()->getDisableShare();
        return ($wishlistId && $this->getWishlist()->getSharingCode() && !$wishlistShareDisabled)  ? true : false;
    }

    /**
     * @return string
     */
    public function getShareUrl()
    {
        $shareCode = $this->getWishlist()->getSharingCode();
        return $this->getUrl('wp_collection/share/' . $shareCode, ['_secure' => true]);
    }

    public function getShareTitle()
    {
        $wishlistName = $this->getWishlist()->getWishlistName();
        $customerName = $this->customerSession->getCustomer()->getName();
        return $wishlistName . ' ' . __('Collection by') . ' ' . $customerName;
    }

    /**
     * @return string
     */
    public function getSharethisJavascript() {
        return $this->wpHelper->getShareJavascript();
    }
}

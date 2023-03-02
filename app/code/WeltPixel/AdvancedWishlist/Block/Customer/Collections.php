<?php

namespace WeltPixel\AdvancedWishlist\Block\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Model\Wishlist as WishlistModel;
use WeltPixel\AdvancedWishlist\Helper\Data as AdvancedWishlistHelper;
use WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider;

/**
 * Class Collections
 * @package WeltPixel\AdvancedWishlist\Block\Customer
 */
class Collections extends Template implements IdentityInterface
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var integer
     */
    protected $profileId;

    /**
     * @var integer
     */
    protected $loggedInCustomerId;

    /**
     * @var MultipleWishlistProvider
     */
    protected $multipleWishlistProvider;

    /**
     * @var AdvancedWishlistHelper
     */
    protected $advancedWishlistHelper;

    /**
     * @var bool
     */
    protected $canEditWishlistFlag = false;

    /**
     * Constructor
     *
     * @param Context $context
     * @param MultipleWishlistProvider $multipleWishlistProvider
     * @param AdvancedWishlistHelper $advancedWishlistHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        MultipleWishlistProvider $multipleWishlistProvider,
        AdvancedWishlistHelper $advancedWishlistHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->multipleWishlistProvider = $multipleWishlistProvider;
        $this->advancedWishlistHelper = $advancedWishlistHelper;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param integer $profileId
     * @return $this
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
        return $this;
    }

    /**
     * @param $flag
     * @return $this
     */
    public function setCanEditWishlistFlag($flag)
    {
        $this->canEditWishlistFlag = $flag;
        return $this;
    }

    /**
     * @param $loggedInCustomerId
     * @return $this
     */
    public function setLoggedInCustomerId($loggedInCustomerId)
    {
        $this->loggedInCustomerId = $loggedInCustomerId;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return int
     */
    public function getLoggedInCustomerId()
    {
        return $this->loggedInCustomerId;
    }

    /**
     * @return bool
     */
    public function canEditWishlist()
    {
        return ($this->canEditWishlistFlag && ($this->getLoggedInCustomerId() == $this->getCustomer()->getId()));
    }

    /**
     * @return string
     */
    public function getEditableVerificationUrl()
    {
        return $this->getUrl('wp_collection/view/editable', ['_secure' => true]);
    }

    /**
     * @return array
     */
    public function getWishlistsForCustomer()
    {
        $isPublicWishlistEnabled = $this->advancedWishlistHelper->isPublicWishlistEnabled();
        if (!$isPublicWishlistEnabled) {
            return [];
        }
        $customer = $this->getCustomer();
        if (!$customer->getId()) {
            return [];
        }

        return $this->multipleWishlistProvider->getWishlistsForCustomer($customer->getId(), $this->canEditWishlistFlag);
    }

    /**
     * @return bool
     */
    public function isShareWishlistEnabled()
    {
        return $this->advancedWishlistHelper->isShareWishlistEnabled();
    }

    /**
     * @return bool
     */
    public function isPriceAlertEnabled()
    {
        return $this->advancedWishlistHelper->isPriceAlertEnabled();
    }

    /**
     * @return bool
     */
    public function isPublicWishlistEnabled()
    {
        return $this->advancedWishlistHelper->isPublicWishlistEnabled();
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getWishlistsForCustomer() as $wishlist) {
            $identities[] = WishlistModel::CACHE_TAG . '_' . $wishlist->getId();
        }

        if ($this->getProfileId()) {
            $identities[] =  'weltpixel_userprofile_' . $this->getProfileId();
        }

        return $identities;
    }
}

<?php

namespace WeltPixel\AdvancedWishlist\Block\Wishlist;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Wishlist\Model\Wishlist as WishlistModel;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;
use WeltPixel\AdvancedWishlist\Model\MultipleWishlistProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ProductList extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * @var MultipleWishlistProvider
     */
    protected $_multipleWishlistProvider;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var WishlistModel
     */
    protected $wishlistModel = null;


    /**
     * @param WishlistHelper $helper
     * @param MultipleWishlistProvider $multipleWishlistProvider
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(WishlistHelper $helper,
                                MultipleWishlistProvider $multipleWishlistProvider,
                                CustomerRepositoryInterface $customerRepository,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_multipleWishlistProvider = $multipleWishlistProvider;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param WishlistModel $wishlistModel
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignWishlistModel($wishlistModel) {
        if ($wishlistModel->getId()) {
            $this->wishlistModel = $wishlistModel;
            $customerId = $wishlistModel->getCustomerId();
            $customerModel = $this->customerRepository->getById($customerId);
            $customerName = $customerModel->getFirstname() . " " . $customerModel->getLastname();
            $wishlistTitle = __('Wishlist');
            if ($this->_helper->isMultiWishlistEnabled()) {
                $wishlistTitle = $wishlistModel->getWishlistName();
            }
            $this->setWishlistTitle($wishlistTitle);
            $this->setCustomerName($customerName);
        } else {
            $this->setWishlistTitle(__('Wishlist Not Available'));
        }
        return $this;
    }

    /**
     * @return WishlistModel
     */
    public function getWishlistModel() {
        return $this->wishlistModel;
    }

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    public function getWishlistItems() {
        $wishlistModel = $this->getWishlistModel();
        if (!$wishlistModel) {
            return [];
        }
        $items = $wishlistModel->getItemCollection()->clear()->setOrder('added_at');
        return $items;
    }

    /**
     * Retrieve table column object list
     *
     * @return \Magento\Wishlist\Block\Customer\Wishlist\Item\Column[]
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if ($child instanceof \Magento\Wishlist\Block\Customer\Wishlist\Item\Column && $child->isEnabled()) {
                $columns[] = $child;
            }
        }
        return $columns;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities() {
        $identities = [];

        foreach ($this->getWishlistItems() as $item) {
            $identities = array_merge($identities, $item->getProduct()->getIdentities());
        }

        $wishlist = $this->getWishlistModel();
        if ($wishlist) {
            $identities[] = WishlistModel::CACHE_TAG . '_' . $wishlist->getId();
        }
        return $identities;
    }

}

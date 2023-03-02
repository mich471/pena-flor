<?php

namespace WeltPixel\UserProfile\Block\Customer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Review\Model\ResourceModel\Review\Product\Collection as ReviewProductCollection;
use Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory as ReviewProductCollectionFactory;
use Magento\Review\Model\Review;
use WeltPixel\UserProfile\Model\UserProfile;
use WeltPixel\UserProfile\Helper\Renderer as ProfileRendererHelper;
use Magento\Catalog\Block\Product\ImageFactory;

/**
 * Class Reviews
 * @package WeltPixel\UserProfile\Block\Customer
 */
class Reviews extends Template implements IdentityInterface
{

    /**
     * @var ProfileRendererHelper
     */
    protected $profileRendererHelper;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var ReviewProductCollection
     */
    protected $collection;

    /**
     * @var ReviewProductCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var UserProfile
     */
    protected $userProfile;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var array
     */
    protected $productCacheData;


    /**
     * Constructor
     *
     * @param Context $context
     * @param ReviewProductCollectionFactory $collectionFactory
     * @param ProfileRendererHelper $profileRendererHelper
     * @param ImageFactory $imageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReviewProductCollectionFactory $collectionFactory,
        ProfileRendererHelper $profileRendererHelper,
        ImageFactory $imageFactory,
        ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->profileRendererHelper = $profileRendererHelper;
        $this->imageFactory = $imageFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param UserProfile $userProfile
     * @return $this
     */
    public function setUserProfile($userProfile)
    {
        $this->userProfile = $userProfile;
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
     * @return UserProfile
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        $userProfile = $this->getUserProfile();
        $identities[] = UserProfile::CACHE_TAG . '_' . $userProfile->getId();

        $reviews = $this->getReviews();

        foreach ($reviews as $review) {
            if ($review->getEntityPkValue()) {
                $identities[] = Product::CACHE_TAG . '_' . $review->getEntityPkValue();
            }
        }

        return $identities;
    }

    /**
     * Get reviews
     *
     * @return bool|Collection
     */
    public function getReviews()
    {
        if (!($customerId = $this->getCustomer()->getId())) {
            return false;
        }
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addCustomerFilter($customerId)
                ->addStatusFilter(Review::STATUS_APPROVED)
                ->setDateOrder();
        }

        return $this->collection;
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductUrl($product)
    {
        return $product->getProductUrl();
    }

    /**
     * @param Product $review
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($review)
    {
        $productId = $review->getEntityPkValue();
        if (!isset($this->productCacheData[$productId])) {
            $product = $this->productRepository->getById($review->getEntityPkValue());
            $this->productCacheData[$productId] = $this->imageFactory->create($product, 'weltpixel_userprofile_review', [])->toHtml();
        }

        return $this->productCacheData[$productId];

    }

    /**
     * @return string
     */
    public function getUserProfileName()
    {
        return $this->profileRendererHelper->getProfileName($this->getUserProfile());
    }

    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $reviews = $this->getReviews();
        if ($reviews) {
            $reviews->load()->addReviewSummary();
        }
        return parent::_beforeToHtml();
    }

}

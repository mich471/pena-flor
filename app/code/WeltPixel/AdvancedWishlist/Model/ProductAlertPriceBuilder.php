<?php
namespace WeltPixel\AdvancedWishlist\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Wishlist\Model\Wishlist;
use Psr\Log\LoggerInterface;
use WeltPixel\AdvancedWishlist\Model\PriceAlert;
use WeltPixel\AdvancedWishlist\Model\PriceAlertFactory;
use Magento\Wishlist\Model\WishlistFactory;
use WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert\CollectionFactory as PriceAlertCollectionFactory;


class ProductAlertPriceBuilder
{

    /**
     * @var PriceAlert
     */
    protected $priceAlert;

    /**
     * @var PriceAlertFactory
     */
    protected $priceAlertFactory;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var PriceAlertCollectionFactory
     */
    protected $priceAlertCollectionFactory;

    /**
     * @var  ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param PriceAlert $priceAlert
     * @param PriceAlertFactory $priceAlertFactory
     * @param WishlistFactory $wishlistFactory
     * @param PriceAlertCollectionFactory $priceAlertCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        PriceAlert $priceAlert,
        PriceAlertFactory $priceAlertFactory,
        WishlistFactory $wishlistFactory,
        PriceAlertCollectionFactory $priceAlertCollectionFactory,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    )
    {
        $this->priceAlert = $priceAlert;
        $this->priceAlertFactory = $priceAlertFactory;
        $this->wishlistFactory = $wishlistFactory;
        $this->priceAlertCollectionFactory = $priceAlertCollectionFactory;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param mixed $wishlist
     * @param int|null $websiteId
     */
    public function refreshProductAlertsForWishlist($wishlist, $websiteId = null)
    {
        if (is_int($wishlist)) {
            $wishlist = $this->wishlistFactory->create()->load($wishlist);
        }
        $disablePriceAlert = $wishlist->getDisablePriceAlert();

        if ($disablePriceAlert) {
            /** Remove the wishlist products from pricealert if it was disabled */
            try {
                $this->priceAlert->deleteWishlist($wishlist->getId(), $websiteId);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        } else {
            /** Update the price alert table with the new data  */
            $wishlistProductIds = $this->getProductIdsForWishlist($wishlist);
            $priceAlertProductIds = $this->getProductIdsFromPriceAlert($wishlist, $websiteId);

            $productsToDelete = array_diff($priceAlertProductIds, $wishlistProductIds);
            try {
                $this->priceAlert->deleteProductsFromWishlist($wishlist->getId(), $productsToDelete);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }

            $productsToAdd = array_diff($wishlistProductIds,$priceAlertProductIds);
            foreach ($productsToAdd as $productId) {
                $product = $this->productRepository->getById($productId);
                $productPrice = $product->getFinalPrice();

                if (in_array($product->getTypeId(), ['bundle', 'grouped'])) {
                    $priceInfo = $product->getPriceInfo()->getPrice('final_price');
                    $productPrice = $priceInfo->getMinimalPrice()->getValue();
                }

                $priceAlertModel = $this->priceAlertFactory->create()
                    ->setCustomerId($wishlist->getCustomerId())
                    ->setProductId($productId)
                    ->setPrice($productPrice)
                    ->setWebsiteId($websiteId)
                    ->setWishlistId($wishlist->getId());

                try {
                    $priceAlertModel->save();
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     */
    public function clearPriceAlertsForCustomer($customerId, $websiteId) {
        try {
            $this->priceAlert->deleteProductsFromCustomer($customerId, $websiteId);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * @param Wishlist $wishlist
     * @return array
     */
    protected function getProductIdsForWishlist($wishlist)
    {
        $wishlistItems = $wishlist->getItemCollection()->getItems();
        $productIds = [];
        foreach ($wishlistItems as $item) {
            $productIds[] = $item->getProduct()->getId();
        }
        $productIds = array_unique($productIds);

        return $productIds;
    }

    /**
     * @param Wishlist $wishlist
     * @param int $websiteId
     * @return array
     */
    protected function getProductIdsFromPriceAlert($wishlist, $websiteId)
    {
        $productIds = [];
        $priceAlertCollection = $this->priceAlertCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addWishlistFilter($wishlist->getId());

        foreach ($priceAlertCollection as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }
}

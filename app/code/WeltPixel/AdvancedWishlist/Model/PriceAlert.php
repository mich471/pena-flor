<?php
namespace WeltPixel\AdvancedWishlist\Model;

/**
 * Class PriceAlert
 *
 * @method int getAlertId()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setAlertId(int $value)
 * @method int getCustomerId()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setCustomerId(int $value)
 * @method int getProductId()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setProductId(int $value)
 * @method float getPrice()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setPrice(float $value)
 * @method int getWebsiteId()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setWebsiteId(int $value)
 * @method int getWishlistId()
 * @method \WeltPixel\AdvancedWishlist\Model\PriceAlert setWishlistId(int $value)
 *
 * @package WeltPixel\AdvancedWishlist\Model
 */
class PriceAlert extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'advancedwishlist_pricealert';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert');
    }

    /**
     * @param int $wishlistId
     * @param int $websiteId
     * @return $this
     */
    public function deleteWishlist($wishlistId, $websiteId = 0)
    {
        $this->getResource()->deleteWishlist($this, $wishlistId, $websiteId);
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }

    /**
     * @param int $wishlistId
     * @param aray $productIds
     * @return $this
     */
    public function deleteProductsFromWishlist($wishlistId, $productIds) {
        $this->getResource()->deleteProductsFromWishlist($this, $wishlistId, $productIds);
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteProductsFromCustomer($customerId, $websiteId) {
        $this->getResource()->deleteProductsFromCustomer($this, $customerId, $websiteId);
        return $this;
    }
}

<?php
namespace WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert;

/**
 * Class Collection
 * @package WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'alert_id';

    /**
     * Define collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\AdvancedWishlist\Model\PriceAlert', 'WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert');
    }

    /**
     * Add website filter
     *
     * @param integer $websiteId
     * @return $this
     */
    public function addWebsiteFilter($websiteId)
    {
        if ($websiteId === null || $websiteId == 0) {
            return $this;
        }
        $condition = $this->getConnection()->quoteInto('website_id=?', $websiteId);
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param integer $wishlistId
     * @return $this
     */
    public function addWishlistFilter($wishlistId)
    {
        if ($wishlistId === null || $wishlistId == 0) {
            return $this;
        }
        $condition = $this->getConnection()->quoteInto('wishlist_id=?', $wishlistId);
        $this->addFilter('wishlist_id', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer and wishlist
     *
     * @param string $sort
     * @return $this
     */
    public function setCustomerAndWishlistOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort)
            ->order('wishlist_id ' . $sort);
        return $this;
    }
}

<?php
namespace WeltPixel\AdvancedWishlist\Model\ResourceModel;

/**
 * Class PriceAlert
 * @package WeltPixel\AdvancedWishlist\Model\ResourceModel
 */
class PriceAlert extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wishlist_product_alert_price', 'alert_id');
    }

    /**
     * Delete all product pice alerts for wishlist on website
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $wishlistId
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteWishlist(\Magento\Framework\Model\AbstractModel $object, $wishlistId, $websiteId = null)
    {
        $connection = $this->getConnection();
        $where = [];
        $where[] = $connection->quoteInto('wishlist_id=?', $wishlistId);
        if ($websiteId) {
            $where[] = $connection->quoteInto('website_id=?', $websiteId);
        }
        $connection->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * Delete all product pice alerts for customer on website
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteCustomer(\Magento\Framework\Model\AbstractModel $object, $customerId, $websiteId = null)
    {
        $connection = $this->getConnection();
        $where = [];
        $where[] = $connection->quoteInto('customer_id=?', $customerId);
        if ($websiteId) {
            $where[] = $connection->quoteInto('website_id=?', $websiteId);
        }
        $connection->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $wishlistId
     * @param array $productIds
     * @return $this
     */
    public function deleteProductsFromWishlist(\Magento\Framework\Model\AbstractModel $object, $wishlistId, $productIds)
    {
        $connection = $this->getConnection();
        $where = [];
        $where[] = $connection->quoteInto('wishlist_id=?', $wishlistId);
        $where[] = $connection->quoteInto('product_id in (?) ', implode(",", $productIds));
        $connection->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteProductsFromCustomer(\Magento\Framework\Model\AbstractModel $object, $customerId, $websiteId)
    {
        $connection = $this->getConnection();
        $where = [];
        $where[] = $connection->quoteInto('customer_id=?', $customerId);
        $where[] = $connection->quoteInto('website_id=?', $websiteId);
        $connection->delete($this->getMainTable(), $where);
        return $this;
    }

}

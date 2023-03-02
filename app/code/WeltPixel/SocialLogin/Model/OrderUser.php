<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */


namespace WeltPixel\SocialLogin\Model;

/**
 * Class OrderUser
 * @package WeltPixel\SocialLogin\Model
 */
class OrderUser extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'weltpixel_sociallogin_orderuser';
    /**
     * @var string
     */
    protected $_cacheTag = 'weltpixel_sociallogin_orderuser';
    /**
     * @var string
     */
    protected $_eventPrefix = 'weltpixel_sociallogin_orderuser';

    protected function _construct()
    {
        $this->_init('WeltPixel\SocialLogin\Model\ResourceModel\OrderUser');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $orderId
     * @param $userId
     * @param $customerId
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setOrderUser($orderId, $userId, $customerId, $type)
    {
        $this->setData([
            'order_id' => $orderId,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'type' => $type
        ])
            ->setId(null)
            ->save();

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderIdsArr() {
        $orderIds = [];
        $collection = $this->getCollection();
        foreach($collection as $row) {
            if(!in_array($row->getOrderId(), $orderIds)) {
                $orderIds[] = $row->getOrderId();
            }
        }

        return $orderIds;
    }

    /**
     * @param $type
     * @return array
     */
    public function getOrderIdsByType($type) {
        $orderIds = [];
        $collection = $this->getCollection()->addFieldToFilter('type', $type);
        foreach($collection as $row) {
            if(!in_array($row->getOrderId(), $orderIds)) {
                $orderIds[] = $row->getOrderId();
            }
        }

        return $orderIds;
    }

}
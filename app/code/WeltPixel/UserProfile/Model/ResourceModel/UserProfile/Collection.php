<?php

namespace WeltPixel\UserProfile\Model\ResourceModel\UserProfile;

/**
 * Class Collection
 * @package WeltPixel\UserProfile\Model\ResourceModel\UserProfile
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'profile_id';

    /**
     * Define collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\UserProfile\Model\UserProfile', 'WeltPixel\UserProfile\Model\ResourceModel\UserProfile');
    }
}

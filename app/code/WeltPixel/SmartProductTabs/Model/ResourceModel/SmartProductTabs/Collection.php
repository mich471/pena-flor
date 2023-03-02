<?php
namespace WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs;

/**
 * Class Collection
 * @package WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs
 */
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\SmartProductTabs\Model\SmartProductTabs', 'WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs');
    }
}

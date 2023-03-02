<?php
namespace WeltPixel\SpeedOptimization\Model\ResourceModel\JsBundling;

/**
 * Class Collection
 * @package WeltPixel\SpeedOptimization\Model\ResourceModel\Sitemap
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
        $this->_init('WeltPixel\SpeedOptimization\Model\JsBundling', 'WeltPixel\SpeedOptimization\Model\ResourceModel\JsBundling');
    }
}

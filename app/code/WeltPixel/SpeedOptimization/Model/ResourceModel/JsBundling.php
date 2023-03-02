<?php
namespace WeltPixel\SpeedOptimization\Model\ResourceModel;

/**
 * Class JsBundling
 * @package WeltPixel\SpeedOptimization\Model\ResourceModel
 */
class JsBundling extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_speedoptimization_jsbundling', 'id');
    }
}

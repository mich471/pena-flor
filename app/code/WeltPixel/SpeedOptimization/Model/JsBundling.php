<?php
namespace WeltPixel\SpeedOptimization\Model;

/**
 * Class JsBundling
 * @package WeltPixel\SpeedOptimization\Model
 */
class JsBundling extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'weltixel_jsbundling';

    /**
     * @var string
     */
    protected $_cacheTag = 'weltixel_jsbundling';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'weltixel_jsbundling';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\SpeedOptimization\Model\ResourceModel\JsBundling');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }
}

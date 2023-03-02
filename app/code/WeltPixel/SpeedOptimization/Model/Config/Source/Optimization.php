<?php
namespace WeltPixel\SpeedOptimization\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Optimization implements \Magento\Framework\Option\ArrayInterface
{
    const OPTIMIZATION_NONE = 0;
    const OPTIMIZATION_UGLIFY = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => self::OPTIMIZATION_UGLIFY, 'label' => __('Uglify')], ['value' => self::OPTIMIZATION_NONE, 'label' => __('None')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::OPTIMIZATION_NONE => __('None'), self::OPTIMIZATION_UGLIFY => __('Uglify')];
    }
}

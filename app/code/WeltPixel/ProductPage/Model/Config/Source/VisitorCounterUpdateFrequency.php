<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class VisitorCounterUpdateFrequency
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class VisitorCounterUpdateFrequency implements ArrayInterface
{
    const UPDATE_REALTIME = 'realtime';
    const UPDATE_PAGEREFRESH = 'pagerefresh';

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::UPDATE_REALTIME,
                'label' => __('Realtime live update')
            ),
            array(
                'value' => self::UPDATE_PAGEREFRESH,
                'label' => __('Page Refresh')
            )
        );
    }
}

<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ZoomType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class ZoomType implements ArrayInterface
{

    /**
     * Return list of ZoomType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'outside',
                'label' => __('Outside')
            ),
            array(
                'value' => 'inside',
                'label' => __('Inside')
            )
        );
    }
}

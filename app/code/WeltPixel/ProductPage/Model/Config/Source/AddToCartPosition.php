<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QtyType
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class AddToCartPosition implements ArrayInterface
{

    /**
     * Return list of QtyType Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'actions',
                'label' => __('On the same line with QTY selector/input.')
            ),
            array(
                'value' => 'actions-bottom',
                'label' => __('Under QTY selector/input')
            )
        );
    }
}

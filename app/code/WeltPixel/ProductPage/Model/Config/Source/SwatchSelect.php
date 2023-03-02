<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SwatchSelect
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class SwatchSelect implements ArrayInterface
{

    const SWATCH_SELECT_DEFAULT = 0;
    const SWATCH_SELECT_ONLY_ONE = 1;
    const SWATCH_SELECT_FIRST = 2;

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SWATCH_SELECT_DEFAULT,
                'label' => __('Default (No Preselect)'),
            ],
            [
                'value' => self::SWATCH_SELECT_ONLY_ONE,
                'label' => __('If only one option available'),
            ],
            [
                'value' => self::SWATCH_SELECT_FIRST,
                'label' => __('First Swatch Always'),
            ]
        ];
    }
}

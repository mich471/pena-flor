<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PrevNextProductName
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class PrevNextProductName implements ArrayInterface
{
    const OPTION_INLINE = 'inline';
    const OPTION_TOOLTIP = 'tooltip';

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::OPTION_INLINE,
                'label' => __('Inline with the next/prev arrows')
            ),
            array(
                'value' => self::OPTION_TOOLTIP,
                'label' => __('As tooltip on hover over the next/prev arrows')
            )
        );
    }
}

<?php

namespace WeltPixel\ProductPage\Model\Config\Source\Gallery;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ConfigurableStrategy
 *
 * @package WeltPixel\ProductPage\Model\Config\Source\Gallery
 */
class ConfigurableStrategy implements ArrayInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'prepend',
                'label' => __('Prepend')
            ],
            [
                'value' => 'replace',
                'label' => __('Replace')
            ]
        ];
    }
}

<?php


namespace WeltPixel\CustomHeader\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class GlobalMessagePosition implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'above_menu', 'label' => 'Above Menu'],
            ['value' => 'below_menu', 'label' => 'Below Menu']
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'above_menu' => 'Above Menu',
            'below_menu' => 'Below Menu'
        ];
    }

}

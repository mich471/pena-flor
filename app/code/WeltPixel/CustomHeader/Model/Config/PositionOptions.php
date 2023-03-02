<?php


namespace WeltPixel\CustomHeader\Model\Config;

class PositionOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'left', 'label' => 'Left'],
            ['value' => 'center', 'label' => 'Center']
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
            'left' => 'Left',
            'center' => 'Center'
        ];
    }
}

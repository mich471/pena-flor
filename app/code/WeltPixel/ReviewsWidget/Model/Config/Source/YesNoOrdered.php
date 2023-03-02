<?php
namespace WeltPixel\ReviewsWidget\Model\Config\Source;

class YesNoOrdered implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'Yes'],
            ['value' => '0', 'label' => 'No']
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
            '1' => 'Yes',
            '0' => 'No'
        ];
    }
}

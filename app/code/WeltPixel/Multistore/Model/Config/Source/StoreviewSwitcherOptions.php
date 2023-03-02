<?php


namespace WeltPixel\Multistore\Model\Config\Source;


class StoreviewSwitcherOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'store_image', 'label' => 'Store Image'],
            ['value' => 'store_name', 'label' => 'Store Name'],
            ['value' => 'both', 'label' => 'Both']
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
            'store_image' => 'Store Image',
            'store_name' => 'Store Name',
            'both' => 'Both'
        ];
    }
}

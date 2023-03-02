<?php

namespace WeltPixel\DesignElements\Model\Config\Source;


class BackToTopMobileOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => 'Display Default'],
            ['value' => 'advanced', 'label' => 'Display Advanced'],
            ['value' => 'hide', 'label' => 'Hide']
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
            'default' => 'Display Default',
            'advanced' => 'Display Advanced',
            'hide' => 'Hide'
        ];
    }
}

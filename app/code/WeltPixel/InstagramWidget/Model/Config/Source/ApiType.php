<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class ApiType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'basic_api', 'label' => __('Basic API')],
            ['value' => 'javascript_parser', 'label' => __('Javascript Fetching (Deprecated)')],
            ['value' => 'old', 'label' => __('Old Api (Deprecated)')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'basic_api' => __('Basic API'),
            'javascript_parser' => __('Javascript Fetching (Deprecated)'),
            'old' => __('Old Api (Deprecated)')
        ];
    }
}

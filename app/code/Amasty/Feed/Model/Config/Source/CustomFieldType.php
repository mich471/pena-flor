<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CustomFieldType implements ArrayInterface
{
    public const ATTRIBUTE = 0;
    public const CUSTOM_TEXT = 1;
    public const MERGED_ATTRIBUTES = 2;

    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [
            self::ATTRIBUTE => __('Attribute'),
            self::CUSTOM_TEXT => __('Custom Text'),
            self::MERGED_ATTRIBUTES => __('Merged Attributes')
        ];

        return $options;
    }
}

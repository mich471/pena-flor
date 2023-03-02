<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class FeedStatus implements ArrayInterface
{
    /**#@+
     * Feed status
     */
    public const FAILED = 3;
    public const GENERATE_NEXT_CRON = 4;
    public const PROCESSING = 2;
    public const READY = 1;
    public const NOT_GENERATED = 0;
    /**#@-*/

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
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
        return [
            self::FAILED => __('Failed'),
            self::PROCESSING => __('Processing'),
            self::READY => __('Ready'),
            self::NOT_GENERATED => __('Not yet Generated'),
            self::GENERATE_NEXT_CRON => __('Will be generated on next cron run')
        ];
    }
}

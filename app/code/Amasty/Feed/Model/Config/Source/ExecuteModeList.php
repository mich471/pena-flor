<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

class ExecuteModeList implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * Feed generation types
     */
    public const CRON = 'schedule';

    public const MANUAL = 'manual';

    public const CRON_GENERATED = 'By Schedule';

    public const MANUAL_GENERATED = 'Manually';
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
     * @return array
     */
    public function toArray()
    {
        return [
            self::MANUAL => __('Manually'),
            self::CRON => __('By Schedule'),
        ];
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Path implements ArrayInterface
{
    public const USE_DEFAULT = 0;
    public const USE_SHORTEST = 1;
    public const USE_LONGEST = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::USE_DEFAULT, 'label' => __('Default Rules')],
            ['value' => self::USE_SHORTEST, 'label' => __('Shortest Path')],
            ['value' => self::USE_LONGEST, 'label' => __('Longest Path')],
        ];
    }
}

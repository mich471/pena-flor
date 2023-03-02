<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed;

use Magento\Framework\Data\OptionSourceInterface;

class Format implements OptionSourceInterface
{
    public const AS_IS = 'as_is';
    public const DATE = 'date';
    public const PRICE = 'price';

    public function toOptionArray(): array
    {
        $result = [];

        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

    public function toArray(): array
    {
        return [
            self::AS_IS => __('As Is'),
            self::DATE => __('Date'),
            self::PRICE => __('Price')
        ];
    }
}

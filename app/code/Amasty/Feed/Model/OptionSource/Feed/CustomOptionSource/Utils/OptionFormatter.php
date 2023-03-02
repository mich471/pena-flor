<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource\Utils;

class OptionFormatter
{
    public function getTitle(string $title, string $code): string
    {
        return $title . ' [' . $code . ']';
    }

    public function getCode(string $code, string $type): string
    {
        return $type . '|' . $code;
    }
}

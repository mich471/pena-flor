<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource\Utils;

class ArrayCustomizer
{
    private const CODE = 'code';
    private const NAME = 'name';

    /**
     * @var OptionFormatter
     */
    private $optionFormatter;

    public function __construct(
        OptionFormatter $optionFormatter
    ) {
        $this->optionFormatter = $optionFormatter;
    }

    public function customizeArray(array $array, string $type, bool $useKey = true): array
    {
        $result = [];
        if ($useKey) {
            foreach ($array as $code => $title) {
                $result[$this->optionFormatter->getCode($code, $type)]
                    = $this->optionFormatter->getTitle((string)$title, $code);
            }
        } else {
            foreach ($array as $item) {
                $result[$this->optionFormatter->getCode($item[self::CODE], $type)]
                    = $this->optionFormatter->getTitle((string)$item[self::NAME], $item[self::CODE]);
            }
        }

        return $result;
    }
}

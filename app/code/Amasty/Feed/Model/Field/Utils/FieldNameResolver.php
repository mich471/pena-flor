<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\Utils;

class FieldNameResolver
{
    public const TYPE_BY_CONDITIONS = 'result';
    public const TYPE_DEFAULT = 'default';

    /**
     * @var array
     */
    private $fieldNamesMap;

    public function __construct(array $fieldNamesMap = [])
    {
        $this->fieldNamesMap = $fieldNamesMap;
    }

    public function getResultFieldName(string $type): string
    {
        return $this->fieldNamesMap[$type]['resultFieldName'] ?? '';
    }

    public function getRuleFieldName(string $type): string
    {
        return $this->fieldNamesMap[$type]['ruleFieldName'] ?? '';
    }
}

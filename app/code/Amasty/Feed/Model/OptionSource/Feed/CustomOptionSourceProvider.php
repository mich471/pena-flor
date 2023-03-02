<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed;

class CustomOptionSourceProvider
{
    public const KEY_SOURCE = 'source';
    public const KEY_LABEL = 'label';

    /**
     * @var array
     */
    private $optionSources = [];

    public function __construct(
        array $optionSources = []
    ) {
        $this->initializeOptionSources($optionSources);
    }

    public function getSources(): array
    {
        return $this->optionSources;
    }

    private function initializeOptionSources(array $optionSources): void
    {
        foreach ($optionSources as $sourceCode => $sourceData) {
            $optionSource = $sourceData['optionSource'] ?? null;
            if (!$optionSource instanceof CustomOptionSource\CustomOptionSourceInterface) {
                throw new \LogicException(
                    sprintf(
                        'Custom Option Source must implement %s',
                        CustomOptionSource\CustomOptionSourceInterface::class
                    )
                );
            }

            $this->optionSources[$sourceCode] = [
                self::KEY_SOURCE => $optionSource,
                self::KEY_LABEL => (string)($sourceData['label'] ?? '')
            ];
        }
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed;

use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{
    /**
     * @var CustomOptionSourceProvider
     */
    private $customOptionSourceProvider;

    /**
     * @var array|null
     */
    private $optionsStorage = null;

    public function __construct(
        CustomOptionSourceProvider $customOptionSourceProvider
    ) {
        $this->customOptionSourceProvider = $customOptionSourceProvider;
    }

    public function toOptionArray(): array
    {
        if ($this->optionsStorage === null) {
            $this->optionsStorage = [];
            foreach ($this->customOptionSourceProvider->getSources() as $key => $source) {
                $this->optionsStorage[$key] = [
                    'label' => (string)$source[CustomOptionSourceProvider::KEY_LABEL],
                    'value' => $this->convertToOptions($source[CustomOptionSourceProvider::KEY_SOURCE]->getOptions())
                ];
            }
        }

        return $this->optionsStorage;
    }

    private function convertToOptions(array $array): array
    {
        $options = [];
        foreach ($array as $value => $label) {
            $options[] = ['label' => $label, 'value' => $value];
        }

        return $options;
    }
}

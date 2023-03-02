<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class BasicAttribute implements CustomOptionSourceInterface
{
    /**
     * @var Utils\ArrayCustomizer
     */
    private $arrayCustomizer;

    public function __construct(
        Utils\ArrayCustomizer $arrayCustomizer
    ) {
        $this->arrayCustomizer = $arrayCustomizer;
    }

    public function getOptions(): array
    {
        $attributes = [
            'sku' => __('SKU'),
            'product_type' => __('Type'),
            'product_websites' => __('Websites'),
            'created_at' => __('Created'),
            'updated_at' => __('Updated'),
            'product_id' => __('Product ID')
        ];

        return $this->arrayCustomizer->customizeArray($attributes, ExportProduct::PREFIX_BASIC_ATTRIBUTE);
    }
}

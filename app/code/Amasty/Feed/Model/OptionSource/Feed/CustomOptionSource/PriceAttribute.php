<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class PriceAttribute implements CustomOptionSourceInterface
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
            'price' => __('Price'),
            'final_price' => __('Final Price'),
            'regular_price' => __('Regular Price'),
            'min_price' => __('Min Price'),
            'max_price' => __('Max Price'),
            'tax_price' => __('Price with TAX(VAT)'),
            'tax_final_price' => __('Final Price with TAX(VAT)'),
            'tax_min_price' => __('Min Price with TAX(VAT)'),
            'special_price' => __('Special Price')
        ];

        return $this->arrayCustomizer->customizeArray($attributes, ExportProduct::PREFIX_PRICE_ATTRIBUTE);
    }
}

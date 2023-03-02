<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class ProductAttribute implements CustomOptionSourceInterface
{
    /**
     * @var Utils\ArrayCustomizer
     */
    private $arrayCustomizer;

    /**
     * @var ExportProduct
     */
    private $export;

    public function __construct(
        Utils\ArrayCustomizer $arrayCustomizer,
        ExportProduct $export
    ) {
        $this->arrayCustomizer = $arrayCustomizer;
        $this->export = $export;
    }

    public function getOptions(): array
    {
        return $this->arrayCustomizer->customizeArray(
            $this->export->getExportAttrCodesList(),
            ExportProduct::PREFIX_PRODUCT_ATTRIBUTE
        );
    }
}

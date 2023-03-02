<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class ImageAttribute implements CustomOptionSourceInterface
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
            'thumbnail' => __('Thumbnail'),
            'image' => __('Base Image'),
            'small_image' => __('Small Image')
        ];

        return $this->arrayCustomizer->customizeArray($attributes, ExportProduct::PREFIX_IMAGE_ATTRIBUTE);
    }
}

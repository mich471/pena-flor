<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class GalleryAttribute implements CustomOptionSourceInterface
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
            'image_1' => __('Image 1'),
            'image_2' => __('Image 2'),
            'image_3' => __('Image 3'),
            'image_4' => __('Image 4'),
            'image_5' => __('Image 5')
        ];

        return $this->arrayCustomizer->customizeArray($attributes, ExportProduct::PREFIX_GALLERY_ATTRIBUTE);
    }
}

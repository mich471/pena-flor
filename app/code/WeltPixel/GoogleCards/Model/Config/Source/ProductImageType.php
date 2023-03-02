<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ProductImageType
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class ProductImageType implements ArrayInterface
{
    const IMAGE_TYPE_BASE = 'image';
    const IMAGE_TYPE_THUMBNAIL = 'thumbnail';
    const IMAGE_TYPE_SMALL = 'small_image';

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::IMAGE_TYPE_BASE,
                'label' => __('Base')
            ],
            [
                'value' => self::IMAGE_TYPE_THUMBNAIL,
                'label' => __('Thumbnail')
            ],
            [
                'value' => self::IMAGE_TYPE_SMALL,
                'label' => __('Small')
            ]
        ];
    }
}

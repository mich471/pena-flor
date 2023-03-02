<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DisplayOn
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class DisplayOn implements ArrayInterface
{
    const DISPLAY_IMAGE = 'product_image';
    const DISPLAY_NAME = 'product_name';
    const DISPLAY_REVIEW = 'product_review';
    const DISPLAY_PRICE =  'product_price';
    const DISPLAY_ADDTOCART = 'addtocart_button';
    const DISPLAY_ADDTOWISHLIST = 'addto_wishlist';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DISPLAY_IMAGE,
                'label' => __('Product Image'),
            ],
            [
                'value' => self::DISPLAY_NAME,
                'label' => __('Product Name'),
            ],
            [
                'value' => self::DISPLAY_REVIEW,
                'label' => __('Product Review'),
            ],
            [
                'value' => self::DISPLAY_PRICE,
                'label' => __('Product Price'),
            ],
            [
                'value' => self::DISPLAY_ADDTOCART,
                'label' => __('Add To Cart Button'),
            ],
            [
                'value' => self::DISPLAY_ADDTOWISHLIST,
                'label' => __('Add To Wishlist'),
            ]
        ];
    }
}

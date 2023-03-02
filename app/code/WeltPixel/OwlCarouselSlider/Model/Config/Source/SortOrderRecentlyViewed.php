<?php

namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;

class SortOrderRecentlyViewed extends SortOrder
{
    const SORT_LASTPRODUCT_VIEWED_ASC = '8';
    const SORT_LASTPRODUCT_VIEWED_DESC = '9';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Default'),
                'value' => self::SORT_DEFAULT
            ],
            [
                'label' => __('Random'),
                'value' => self::SORT_RANDOM
            ],
            [
                'label' => __('Product ID Ascending'),
                'value' => self::SORT_ID_ASC
            ],
            [
                'label' => __('Product ID Descending'),
                'value' => self::SORT_ID_DESC
            ],
            [
                'label' => __('Price Ascending '),
                'value' => self::SORT_PRICE_ASC
            ],
            [
                'label' => __('Price Descending '),
                'value' => self::SORT_PRICE_DESC
            ],
            [
                'label' => __('Alphabetically Ascending '),
                'value' => self::SORT_NAME_ASC
            ],
            [
                'label' => __('Alphabetically Descending '),
                'value' => self::SORT_NAME_DESC
            ],
            [
                'label' => __('Last Product Viewed Ascending'),
                'value' => self::SORT_LASTPRODUCT_VIEWED_ASC
            ],
            [
                'label' => __('Last Product Viewed Descending'),
                'value' => self::SORT_LASTPRODUCT_VIEWED_DESC
            ]

        ];
    }
}

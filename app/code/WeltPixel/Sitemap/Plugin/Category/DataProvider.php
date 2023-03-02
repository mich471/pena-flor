<?php
namespace WeltPixel\Sitemap\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'weltpixel_options' => [
                'weltpixel_exclude_from_sitemap',
                'wp_enable_index_follow',
                'wp_index_value',
                'wp_follow_value',
                'wp_enable_canonical_url',
                'wp_canonical_url'
            ]
        ];
    }


}

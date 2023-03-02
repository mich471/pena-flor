<?php
namespace WeltPixel\NavigationLinks\Plugin\Category;

class DataProvider extends \WeltPixel\Backend\Plugin\Category\DataProvider
{

    /**
     * Rewrite this in all subclassess, provide the list with category attributes
     * @return array
     */
    protected function _getFieldsMap() {
        return [
            'weltpixel_options' => [
                'weltpixel_category_url',
                'weltpixel_category_url_newtab',
                'weltpixel_sc_layout',
                'weltpixel_sc_columns',
                'weltpixel_sc_title_position',
                'weltpixel_sc_image',
                'weltpixel_sc_hide'
            ],
            'weltpixel_megamenu' => [
                'weltpixel_mm_display_mode',
                'weltpixel_mm_columns_number',
                'weltpixel_mm_column_width',
                'weltpixel_mm_top_block_type',
                'weltpixel_mm_top_block_cms',
                'weltpixel_mm_top_block',
                'weltpixel_mm_right_block_type',
                'weltpixel_mm_right_block_cms',
                'weltpixel_mm_right_block',
                'weltpixel_mm_bottom_block_type',
                'weltpixel_mm_bottom_block_cms',
                'weltpixel_mm_bottom_block',
                'weltpixel_mm_left_block_type',
                'weltpixel_mm_left_block_cms',
                'weltpixel_mm_left_block',
                'weltpixel_mm_mob_hide_allcat',
                'weltpixel_mm_font_color',
                'weltpixel_mm_font_hover_color',
                'weltpixel_mm_show_arrows',
                'weltpixel_mm_dynamic_sc_flag',
                'weltpixel_mm_dynamic_sc_opts',
                'weltpixel_mm_image_enable',
                'weltpixel_mm_image_height',
                'weltpixel_mm_image_width',
                'weltpixel_mm_image_name_align',
                'weltpixel_mm_image',
                'weltpixel_mm_label_text',
                'weltpixel_mm_label_font_color',
                'weltpixel_mm_label_background_color',
                'weltpixel_mm_label_position',
                'weltpixel_mm_image_alt',
                'weltpixel_mm_image_radius',
                'weltpixel_mm_image_position'

            ]
        ];
    }


}

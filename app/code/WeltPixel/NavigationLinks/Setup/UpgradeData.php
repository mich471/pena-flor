<?php

namespace WeltPixel\NavigationLinks\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $catalogSetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->catalogSetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

	    /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
	    $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_category_url_newtab', [
                'type' => 'int',
                'label' => 'Open Link In New Tab',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 2,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);
        }

	    if (version_compare($context->getVersion(), '1.2.0') < 0) {

		    $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_display_mode', [
				    'type' => 'varchar',
				    'label' => 'Display Mode',
				    'input' => 'select',
				    'default' => 'sectioned',
				    'required' => false,
				    'sort_order' => 1,
				    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				    'group' => 'WeltPixel Mega Menu Options'
		    ]);

		    $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_columns_number', [
				    'type' => 'text',
				    'label' => 'Number of columns in dropdown menu',
				    'input' => 'text',
				    'default' => '4',
				    'required' => false,
				    'sort_order' => 2,
				    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				    'wysiwyg_enabled' => false,
				    'group' => 'WeltPixel Mega Menu Options',
		    ]);

		    $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_column_width', [
				    'type' => 'text',
				    'label' => 'Column Width',
				    'input' => 'text',
				    'default' => 'auto',
				    'required' => false,
				    'sort_order' => 3,
				    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				    'wysiwyg_enabled' => false,
				    'group' => 'WeltPixel Mega Menu Options',
		    ]);
	    }

        if (version_compare($context->getVersion(), '1.2.1') < 0) {

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_top_block', [
                'type' => 'text',
                'label' => 'Top Custom HTML',
                'input' => 'text',
                'required' => false,
                'sort_order' => 4,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'group' => 'WeltPixel Mega Menu Options',
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_right_block', [
                'type' => 'text',
                'label' => 'Right Custom HTML',
                'input' => 'text',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'group' => 'WeltPixel Mega Menu Options',
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_bottom_block', [
                'type' => 'text',
                'label' => 'Bottom Custom HTML',
                'input' => 'text',
                'required' => false,
                'sort_order' => 6,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'group' => 'WeltPixel Mega Menu Options',
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_left_block', [
                'type' => 'text',
                'label' => 'Left Custom HTML',
                'input' => 'text',
                'required' => false,
                'sort_order' => 7,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.2') < 0) {

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_top_block_type', [
                'type' => 'varchar',
                'label' => 'Top Block',
                'input' => 'select',
                'default' => 'none',
                'required' => false,
                'sort_order' => 8,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_top_block_cms', [
                'type' => 'varchar',
                'label' => 'Top CMS Block',
                'input' => 'select',
                'required' => false,
                'sort_order' => 9,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_right_block_type', [
                'type' => 'varchar',
                'label' => 'Right Block',
                'input' => 'select',
                'default' => 'none',
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_right_block_cms', [
                'type' => 'varchar',
                'label' => 'Right CMS Block',
                'input' => 'select',
                'required' => false,
                'sort_order' => 11,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_bottom_block_type', [
                'type' => 'varchar',
                'label' => 'Bottom Block',
                'input' => 'select',
                'default' => 'none',
                'required' => false,
                'sort_order' => 12,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_bottom_block_cms', [
                'type' => 'varchar',
                'label' => 'Bottom CMS Block',
                'input' => 'select',
                'required' => false,
                'sort_order' => 13,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_left_block_type', [
                'type' => 'varchar',
                'label' => 'Left Block',
                'input' => 'select',
                'default' => 'none',
                'required' => false,
                'sort_order' => 14,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_left_block_cms', [
                'type' => 'varchar',
                'label' => 'Left CMS Block',
                'input' => 'select',
                'required' => false,
                'sort_order' => 15,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);

        }

        if (version_compare($context->getVersion(), '1.2.3') < 0) {
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_mob_hide_allcat', [
                'type' => 'int',
                'label' => 'Hide Mobile Link "All [category name]"',
                'input' => 'select',
                'required' => false,
                'sort_order' => 16,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Mega Menu Options',
                'default' => 0,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.4') < 0) {
            $catalogSetup->updateAttribute(Category::ENTITY, 'weltpixel_category_url', 'note','', null);
        }

        if (version_compare($context->getVersion(), '1.2.5') < 0) {
            $attributes = [
                'weltpixel_mm_top_block' => [
                    'is_wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true,
                ],
                'weltpixel_mm_right_block' => [
                    'is_wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true,
                ],
                'weltpixel_mm_bottom_block' => [
                    'is_wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true,
                ],
                'weltpixel_mm_left_block' => [
                    'is_wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true,
                ]
            ];

            foreach ($attributes as $attrCode => $fieldValue) {
                foreach ($fieldValue as $field => $value) {
                    $catalogSetup->updateAttribute(Category::ENTITY, $attrCode, $field, $value);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.6') < 0) {
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_font_color', [
                'type' => 'text',
                'label' => 'Font Color',
                'input' => 'text',
                'default' => '',
                'required' => false,
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_font_hover_color', [
                'type' => 'text',
                'label' => 'Font Hover Color',
                'input' => 'text',
                'default' => '',
                'required' => false,
                'sort_order' => 21,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.7') < 0) {

            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_show_arrows', [
                'type' => 'int',
                'label' => 'Show Arrow',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 22,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.8') < 0) {

            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_dynamic_sc_flag', [
                'type' => 'int',
                'label' => 'Dynamic sub-categories distribution',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 23,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options',
            ]);

            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_dynamic_sc_opts', [
                'type' => 'text',
                'label' => 'Dynamic sub-categories',
                'input' => 'text',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 24,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.9') < 0) {
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_sc_layout', [
                'type' => 'varchar',
                'label' => 'Category Layout',
                'input' => 'select',
                'default' => 'default',
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_sc_columns', [
                'type' => 'int',
                'label' => 'Number of Columns',
                'input' => 'select',
                'default' => '4',
                'required' => false,
                'sort_order' => 11,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_sc_title_position', [
                'type' => 'varchar',
                'label' => 'Sub-category title position',
                'input' => 'select',
                'default' => 'under_image',
                'required' => false,
                'sort_order' => 12,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);

            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_sc_image', [
                'type' => 'varchar',
                'label' => 'Subcategory Image',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 12,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);

        }
        if (version_compare($context->getVersion(), '1.2.10') < 0) {
            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_sc_hide', [
                'type' => 'int',
                'label' => 'Hide in Subcategory',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 11,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Options'
            ]);
        }
        if (version_compare($context->getVersion(), '1.2.11') < 0) {
            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_image', [
                'type' => 'varchar',
                'label' => 'Menu Image',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_width', [
                'type' => 'int',
                'label' => 'Menu Image Width',
                'input' => 'text',
                'required' => false,
                'sort_order' => 21,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_height', [
                'type' => 'int',
                'label' => 'Menu Image Height',
                'input' => 'text',
                'required' => false,
                'sort_order' => 22,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }
        if (version_compare($context->getVersion(), '1.2.12') < 0) {
            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_image_enable', [
                'type' => 'int',
                'label' => 'Enable Mega Menu Images',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'sort_order' => 23,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.13') < 0) {
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_name_align', [
                'type' => 'varchar',
                'label' => 'Category Name Alignment',
                'input' => 'select',
                'default' => 'left',
                'required' => false,
                'sort_order' => 24,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.14') < 0) {
            $catalogSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'weltpixel_mm_label_text', [
                'type' => 'varchar',
                'label' => 'Mega Menu Label Text',
                'input' => 'text',
                'required' => false,
                'sort_order' => 25,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_label_font_color', [
                'type' => 'text',
                'label' => 'Mega Menu Label Font Color',
                'input' => 'text',
                'default' => '',
                'required' => false,
                'sort_order' => 26,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_label_background_color', [
                'type' => 'text',
                'label' => 'Mega Menu Label Background Color',
                'input' => 'text',
                'default' => '',
                'required' => false,
                'sort_order' => 27,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_label_position', [
                'type' => 'varchar',
                'label' => 'Mega Menu Label Position',
                'input' => 'select',
                'default' => 'center',
                'required' => false,
                'sort_order' => 28,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_alt', [
                'type' => 'text',
                'label' => 'Mega Menu Image Alt',
                'input' => 'text',
                'required' => false,
                'sort_order' => 29,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
        }

        if (version_compare($context->getVersion(), '1.2.15') < 0) {
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_radius', [
                'type' => 'text',
                'label' => 'Mega Menu Image Radius',
                'input' => 'text',
                'required' => false,
                'sort_order' => 30,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'group' => 'WeltPixel Mega Menu Options',
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, 'weltpixel_mm_image_position', [
                'type' => 'varchar',
                'label' => 'Mega Menu Image Position',
                'input' => 'select',
                'default' => 'top',
                'required' => false,
                'sort_order' => 31,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'WeltPixel Mega Menu Options'
            ]);
        }

        $setup->endSetup();
    }
}

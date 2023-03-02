<?php

namespace WeltPixel\Sitemap\Setup;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
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
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;


    /**
     * UpgradeData constructor.
     * @param CategorySetupFactory $categorySetupFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory,
                                ProductCollectionFactory $productCollectionFactory,
                                CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->catalogSetupFactory = $categorySetupFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);
            $enableIndexFollowAttribute = 'wp_enable_index_follow';
            $indexAttribute = 'wp_index_value';
            $followAttribute = 'wp_follow_value';

            $catalogSetup->addAttribute(Category::ENTITY, $enableIndexFollowAttribute, [
                'type' => 'int',
                'label' => 'Enable Index Follow',
                'input' => 'select',
                'required' => false,
                'sort_order' => 15,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, $indexAttribute, [
                'type' => 'int',
                'label' => 'Index Value',
                'input' => 'select',
                'required' => false,
                'sort_order' => 16,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\IndexValue'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, $followAttribute, [
                'type' => 'int',
                'label' => 'Follow Value',
                'input' => 'select',
                'required' => false,
                'sort_order' => 17,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\FollowValue'
            ]);
            $catalogSetup->addAttribute(Product::ENTITY, $enableIndexFollowAttribute, [
                'type' => 'int',
                'label' => 'Enable Index Follow',
                'input' => 'select',
                'required' => false,
                'sort_order' => 15,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
            ]);
            $catalogSetup->addAttribute(Product::ENTITY, $indexAttribute, [
                'type' => 'int',
                'label' => 'Index Value',
                'input' => 'select',
                'required' => false,
                'sort_order' => 16,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\IndexValue'
            ]);
            $catalogSetup->addAttribute(Product::ENTITY, $followAttribute, [
                'type' => 'int',
                'label' => 'Follow Value',
                'input' => 'select',
                'required' => false,
                'sort_order' => 17,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\FollowValue'
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

            $enableIndexFollowAttribute = 'wp_enable_index_follow';

            $categoryAttribute = $catalogSetup->getAttribute(Category::ENTITY, $enableIndexFollowAttribute);
            $productAttribute = $catalogSetup->getAttribute(Product::ENTITY, $enableIndexFollowAttribute);

            $categoryAttributeId = $categoryAttribute['attribute_id'];
            $productAttributeId = $productAttribute['attribute_id'];

            $categoryEntityIntTable = $setup->getTable('catalog_category_entity_int');
            $productEntityIntTable = $setup->getTable('catalog_product_entity_int');

            /** Enterprise version fix, entity_id column changed to row_id */
            $categoryEntityId = 'entity_id';
            $categoryEntityIntColumns = array_keys($setup->getConnection()->describeTable($categoryEntityIntTable));
            if (in_array('row_id', $categoryEntityIntColumns)) {
                $categoryEntityId = 'row_id';
            }

            $productEntityId = 'entity_id';
            $productEntityIntColumns = array_keys($setup->getConnection()->describeTable($productEntityIntTable));
            if (in_array('row_id', $productEntityIntColumns)) {
                $productEntityId = 'row_id';
            }
            /** Enterprise version fix, entity_id column changed to row_id */

            $productCollection = $this->productCollectionFactory->create();
            $productIds = $productCollection->getAllIds();

            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryIds = $categoryCollection->getAllIds();

            foreach ($categoryIds as $id) {
                try {
                    $setup->getConnection()->query(
                        "INSERT INTO `$categoryEntityIntTable`" .
                        "(`value_id`, `attribute_id`, `store_id`, `" . $categoryEntityId . "`, `value`) " .
                        "VALUES (NULL, '$categoryAttributeId', '0', '$id', '0');");
                } catch (\Exception $ex) {}
            }

            foreach ($productIds as $id) {
                try {
                    $setup->getConnection()->query(
                        "INSERT INTO `$productEntityIntTable`" .
                        "(`value_id`, `attribute_id`, `store_id`, `" . $productEntityId . "`, `value`) " .
                        "VALUES (NULL, '$productAttributeId', '0', '$id', '0');");
                } catch (\Exception $ex) {}
            }
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);
            $enableCanonicalUrlAttribute = 'wp_enable_canonical_url';
            $canonicalUrlAttribute = 'wp_canonical_url';

            $catalogSetup->addAttribute(Category::ENTITY, $enableCanonicalUrlAttribute, [
                'type' => 'int',
                'label' => 'Enable Canonical Url',
                'input' => 'select',
                'required' => false,
                'sort_order' => 25,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
            ]);
            $catalogSetup->addAttribute(Category::ENTITY, $canonicalUrlAttribute, [
                'type' => 'varchar',
                'label' => 'Canonical Url',
                'input' => 'text',
                'required' => false,
                'sort_order' => 26,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => ''
            ]);
            $catalogSetup->addAttribute(Product::ENTITY, $enableCanonicalUrlAttribute, [
                'type' => 'int',
                'label' => 'Enable Canonical Url',
                'input' => 'select',
                'required' => false,
                'sort_order' => 25,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => 0,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
            ]);
            $catalogSetup->addAttribute(Product::ENTITY, $canonicalUrlAttribute, [
                'type' => 'varchar',
                'label' => 'Canonical Url',
                'input' => 'text',
                'required' => false,
                'sort_order' => 26,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'group' => 'WeltPixel Options',
                'default' => ''
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

            $enableCanonicalAttribute = 'wp_enable_canonical_url';

            $categoryAttribute = $catalogSetup->getAttribute(Category::ENTITY, $enableCanonicalAttribute);
            $productAttribute = $catalogSetup->getAttribute(Product::ENTITY, $enableCanonicalAttribute);

            $categoryAttributeId = $categoryAttribute['attribute_id'];
            $productAttributeId = $productAttribute['attribute_id'];

            $categoryEntityIntTable = $setup->getTable('catalog_category_entity_int');
            $productEntityIntTable = $setup->getTable('catalog_product_entity_int');

            /** Enterprise version fix, entity_id column changed to row_id */
            $categoryEntityId = 'entity_id';
            $categoryEntityIntColumns = array_keys($setup->getConnection()->describeTable($categoryEntityIntTable));
            if (in_array('row_id', $categoryEntityIntColumns)) {
                $categoryEntityId = 'row_id';
            }

            $productEntityId = 'entity_id';
            $productEntityIntColumns = array_keys($setup->getConnection()->describeTable($productEntityIntTable));
            if (in_array('row_id', $productEntityIntColumns)) {
                $productEntityId = 'row_id';
            }
            /** Enterprise version fix, entity_id column changed to row_id */

            $productCollection = $this->productCollectionFactory->create();
            $productIds = $productCollection->getAllIds();

            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryIds = $categoryCollection->getAllIds();

            foreach ($categoryIds as $id) {
                try {
                    $setup->getConnection()->query(
                        "INSERT INTO `$categoryEntityIntTable`" .
                        "(`value_id`, `attribute_id`, `store_id`, `" . $categoryEntityId . "`, `value`) " .
                        "VALUES (NULL, '$categoryAttributeId', '0', '$id', '0');");
                } catch (\Exception $ex) {}
            }

            foreach ($productIds as $id) {
                try {
                    $setup->getConnection()->query(
                        "INSERT INTO `$productEntityIntTable`" .
                        "(`value_id`, `attribute_id`, `store_id`, `" . $productEntityId . "`, `value`) " .
                        "VALUES (NULL, '$productAttributeId', '0', '$id', '0');");
                } catch (\Exception $ex) {}
            }
        }

        $installer->endSetup();
    }
}

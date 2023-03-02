<?php

namespace WeltPixel\SmartProductTabs\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('weltpixel_smartproducttabs')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Title'
                )->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                    'Status'
                )->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Status'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Store id'
                )->addColumn(
                    'customer_group',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Group'
                )->addColumn(
                    'content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => ''],
                    'Conditions'
                )->addColumn(
                    'conditions_serialized',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => true, 'default' => ''],
                    'Conditions'
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs'),
                        ['title','content'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['title','content'],
                    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
                )->setComment(
                    'WeltPixel Smart Product Tabs'
                );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            /**
             * Create table 'weltpixel_smartproducttabs_rule_idx'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('weltpixel_smartproducttabs_rule_idx')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Rule Id'
                )->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Id'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Store id'
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs_rule_idx'),
                        ['rule_id']
                    ),
                    ['rule_id']
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs_rule_idx'),
                        ['product_id']
                    ),
                    ['product_id']
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs_rule_idx'),
                        ['store_id']
                    ),
                    ['store_id']
                )->setComment(
                    'WeltPixel Smart Product Tabs Rules Index'
                );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $installer->getTable('weltpixel_smartproducttabs');
            if ($installer->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'name',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 255,
                            'nullable' => true,
                            'comment'  => 'Name',
                            'default'  => '',
                            'after'    => 'id'
                        ]
                    );

                $installer->getConnection()->dropIndex(
                    $installer->getTable('weltpixel_smartproducttabs'),
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs'),
                        ['title','content'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    )
                );

                $installer->getConnection()->addIndex(
                    $installer->getTable('weltpixel_smartproducttabs'),
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_smartproducttabs'),
                        ['name', 'title','content'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['name','title','content'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                );
            }
        }

        $installer->endSetup();
    }
}

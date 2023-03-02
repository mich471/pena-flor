<?php

namespace WeltPixel\SpeedOptimization\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
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
            /**
             * Create table 'weltpixel_speedoptimization_jsbundling'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('weltpixel_speedoptimization_jsbundling'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'themepath',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    ['nullable' => false, 'default' => ''],
                    'Theme Path'
                )->addColumn(
                    'bundling_identifier',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    ['nullable' => false, 'default' => ''],
                    'Bundling Identifier'
                )->addColumn(
                    'requiredfields',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    ['nullable' => false, 'default' => ''],
                    'Bundling Fields'
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_speedoptimization_jsbundling'),
                        ['themepath']
                    ),
                    ['themepath']
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_speedoptimization_jsbundling'),
                        ['bundling_identifier']
                    ),
                    ['bundling_identifier']
                )->setComment(
                    'WeltPixel SpeedOptimization JsBundling'
                );

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}

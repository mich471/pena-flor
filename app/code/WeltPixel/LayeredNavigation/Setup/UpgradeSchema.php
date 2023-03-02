<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_{Module}
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrade Db schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tableName = $setup->getTable('weltpixel_ln_attribute_options');

            if ($setup->getConnection()->isTableExists($tableName)) {
                $columns = [
                    'instant_search' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Instant Search Desktop',
                    ],
                    'instant_search_mobile' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Instant Search Mobile',
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->dropColumn($tableName, $name);
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $tableName = $setup->getTable('weltpixel_ln_attribute_options');

            if ($setup->getConnection()->isTableExists($tableName)) {
                $columns = [
                    'category_visibility' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Category Visibility',
                    ],
                    'category_ids' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'unsigned' => true,
                        'nullable' => true,
                        'comment' => 'Categories ids list',
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->dropColumn($tableName, $name);
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        /** Free Version Upgrade Fallback Fix */
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $setup->getTable('weltpixel_ln_attribute_options');

            if ($setup->getConnection()->isTableExists($tableName)) {
                $columns = [
                    'instant_search' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Instant Search Desktop',
                    ],
                    'instant_search_mobile' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Instant Search Mobile',
                    ],
                    'category_visibility' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'default' => 0,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'Category Visibility',
                    ],
                    'category_ids' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'unsigned' => true,
                        'nullable' => true,
                        'comment' => 'Categories ids list',
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    if (!$connection->tableColumnExists($tableName, $name)) {
                        $connection->addColumn($tableName, $name, $definition);
                    }
                }
            }
        }

        $setup->endSetup();
    }
}

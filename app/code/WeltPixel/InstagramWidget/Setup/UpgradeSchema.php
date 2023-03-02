<?php

namespace WeltPixel\InstagramWidget\Setup;

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

            /**
             * Create table 'weltpixel_instagram_cache'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('weltpixel_instagram_cache')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'cache_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Cache Id'
                )->addColumn(
                    'content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64K',
                    ['nullable' => true, 'default' => ''],
                    'Content'
                )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_instagram_cache'),
                        ['cache_id']
                    ),
                    ['cache_id']
                )->setComment(
                    'WeltPixel Intagram Widget Cache'
                );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            try {
                $tableName = $installer->getTable('weltpixel_instagram_cache');
                $installer->run("ALTER TABLE `$tableName` CHANGE `content` `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL COMMENT 'Content';");
            } catch (\Exception $ex) {
            }
        }

        $installer->endSetup();
    }
}

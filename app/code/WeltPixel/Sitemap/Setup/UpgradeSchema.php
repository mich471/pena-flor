<?php

namespace WeltPixel\Sitemap\Setup;

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
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'wp_enable_index_follow',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Enable Index Follow',
                    'default' => 0
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'wp_index_value',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Index Value',
                    'default' => 0
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'wp_follow_value',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Follow Value',
                    'default' => 0
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'wp_enable_canonical_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Enable Canonical Url',
                    'default' => 0
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_page'),
                'wp_canonical_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Canonical Url',
                    'default' => ''
                ]
            );
        }

        $installer->endSetup();
    }
}

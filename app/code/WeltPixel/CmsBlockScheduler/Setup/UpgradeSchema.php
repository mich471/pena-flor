<?php
namespace WeltPixel\CmsBlockScheduler\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package WeltPixel\CmsBlockScheduler\Setup
 */
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

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('cms_block'),
                'cron_schedule_flag',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Valid From - Valid To Cron Scheduler Flag',
                    'after' => 'tag'
                ]
            );
        }

        $setup->endSetup();
    }
}

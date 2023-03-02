<?php

namespace WeltPixel\CmsBlockScheduler\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        // Get weltpixel_cmsblockscheduler_tags table
        $tableName = $installer->getTable('weltpixel_cmsblockscheduler_tags');
	    
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            /*
             * Create table weltpixel_cmsblockscheduler_tags
             */
            $table = $installer->getConnection()->newTable(
                $tableName
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag Id'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Tag Title'
            )->addIndex(
                $installer->getIdxName('weltpixel_cmsblockscheduler_tags', ['id']),
                ['id']
            )->addIndex(
                $installer->getIdxName('weltpixel_cmsblockscheduler_tags', ['title']),
                ['title']
            );

            $installer->getConnection()->createTable($table);
        }
        
	    // Add Extra Content fields to cms block table
	    $tableName = $setup->getTable('cms_block');
	
	    if ($setup->getConnection()->isTableExists($tableName) == true) {
		    $connection = $setup->getConnection();
		    
		    // before that, remove them if already exists
		    if ($connection->tableColumnExists($tableName, 'valid_from'))
		        $connection->dropColumn($tableName, 'valid_from');
		    if ($connection->tableColumnExists($tableName, 'valid_to'))
		        $connection->dropColumn($tableName, 'valid_to');
		    if ($connection->tableColumnExists($tableName, 'customer_group'))
		        $connection->dropColumn($tableName, 'customer_group');
		    if ($connection->tableColumnExists($tableName, 'tag'))
		        $connection->dropColumn($tableName, 'tag');
		
		    $connection->addColumn(
				    $tableName,
				    'valid_from',
				    [
					    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
					    'unsigned' => true,
					    'nullable' => true,
					    'comment'  => 'Valid From',
					    'default'  => NULL
				    ]
		    );
		
		    $connection->addColumn(
				    $tableName,
				    'valid_to',
				    [
						    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
						    'unsigned' => true,
						    'nullable' => true,
						    'comment' => 'Valid To',
						    'default'  => NULL
				    ]
		    );
		
		    $connection->addColumn(
				    $tableName,
				    'customer_group',
				    [
						    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						    'nullable' => true,
						    'default' => NULL,
						    'comment' => 'Customer Group'
				    ]
		    );
		
		    $connection->addColumn(
				    $tableName,
				    'tag',
				    [
						    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						    'nullable' => true,
						    'default' => NULL,
						    'comment' => 'Tag'
				    ]
		    );
	    }
        
        $installer->endSetup();
    }
}

<?php
namespace WeltPixel\ProductPage\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 */
	public function upgrade(
		SchemaSetupInterface $setup,
		ModuleContextInterface $context
	) {
		$installer = $setup;
		$installer->startSetup();

		if ( version_compare( $context->getVersion(), '1.1.2' ) < 0 ) {
			$table = $installer->getConnection()
			                   ->newTable( $installer->getTable( 'weltpixel_product_view_values' ) )
			                   ->addColumn(
				                   'entity_id',
				                   \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
				                   null,
				                   [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
				                   'Entity Id'
			                   )
				->addColumn(
					'version_id',
					\Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
					11,
					[ 'nullable' => false ],
					'Version Id'
				)
				->addColumn(
					'store_id',
					\Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
					11,
					[ 'nullable' => false ],
					'Store Id'
				)
				->addColumn(
					'values',
					\Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
					'2M',
					[],
					'Values'
				);
			$installer->getConnection()->createTable( $table );
		}

		if (version_compare($context->getVersion(), '1.1.4') < 0) {
            $table = $installer->getConnection()
                ->newTable( $installer->getTable( 'weltpixel_product_visitor_counter' ) )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
                    'Entity Id'
                )
                ->addColumn(
                    'session_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                    64,
                    [ 'nullable' => false ],
                    'Session Id'
                )
                ->addColumn(
                    'product_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    11,
                    [ 'nullable' => false ],
                    'Product Id'
                )
                ->addColumn(
                    'last_visit_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Update Time'
                 )->addIndex(
                    $installer->getIdxName(
                        $installer->getTable('weltpixel_product_visitor_counter'),
                        ['session_id', 'product_id']
                    ),
                    ['session_id', 'product_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
            $installer->getConnection()->createTable( $table );
        }

		$installer->endSetup();

	}
}

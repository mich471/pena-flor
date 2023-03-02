<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup;

use Amasty\Feed\Model\Category\ResourceModel\Category;
use Amasty\Feed\Model\Category\ResourceModel\Mapping;
use Amasty\Feed\Model\Category\ResourceModel\Taxonomy;
use Amasty\Feed\Model\Field\ResourceModel\Condition;
use Amasty\Feed\Model\Field\ResourceModel\Field;
use Amasty\Feed\Model\ResourceModel\Feed;
use Amasty\Feed\Model\Schedule\ResourceModel\Schedule;
use Amasty\Feed\Model\ValidProduct\ResourceModel\ValidProduct;
use Amasty\Feed\Setup\Operation\UpgradeTo200;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->uninstallTables($setup)
            ->uninstallConfigData($setup)
            ->uninstallFlagData($setup)
            ->removeEmailTemplates($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            ValidProduct::TABLE_NAME,
            Schedule::TABLE_NAME,
            Feed::TABLE_NAME,
            Mapping::TABLE_NAME,
            Category::TABLE_NAME,
            Field::TABLE_NAME,
            Condition::TABLE_NAME,
            Taxonomy::TABLE_NAME
        ];
        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            "`path` LIKE 'amasty_feed%'"
        );

        return $this;
    }

    private function uninstallFlagData(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('flag'),
            "`flag_code` LIKE 'amasty_feed_upg%'"
        );

        return $this;
    }

    private function removeEmailTemplates(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('email_template'),
            [
                'orig_template_code IN(?)' => [
                    UpgradeTo200::SUCCESS_TEMPLATE_NAME,
                    UpgradeTo200::UNSUCCESS_TEMPLATE_NAME
                ]
            ]
        );

        return $this;
    }
}

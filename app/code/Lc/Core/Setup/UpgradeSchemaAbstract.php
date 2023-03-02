<?php

namespace Lc\Core\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class InstallSchemaAbstract
 * @description: Esta clase abstrae los simples pasos de cualquier setup, solo es necesario extender de esta clase y crear
 * un metodo con la siguiente nomenclatura: para la version 1.0.0 corresponde nombrar el metodo v1_0_0().
 * @package Lc\Core\Setup
 */
abstract class UpgradeSchemaAbstract extends InstallSchemaAbstract implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->context = $context;
        $this->startSetup();
        $this->runInstallVersion();
        $this->endSetup();
    }
}
<?php

namespace Lc\Core\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchemaAbstract
 * @description: Esta clase abstrae los simples pasos de cualquier setup, solo es necesario extender de esta clase y crear
 * un metodo con la siguiente nomenclatura: para la version 1.0.0 corresponde nombrar el metodo v1_0_0().
 * @package Lc\Core\Setup
 */
abstract class InstallSchemaAbstract implements InstallSchemaInterface
{
    /** @var  SchemaSetupInterface */
    protected $installer;

    /** @var  ModuleContextInterface */
    protected $context;

    /** @var  SchemaSetupInterface */
    protected $setup;

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->context = $context;
        $this->startSetup();
        $this->runInstallVersion();
        $this->endSetup();
    }

    protected function startSetup()
    {
        $this->installer = $this->getSetup();
        $this->installer->startSetup();
        return $this;
    }

    protected function endSetup()
    {
        $this->installer->endSetup();
        return $this;
    }

    protected function runInstallVersion()
    {
        $version = $this->getContext()->getVersion();
        $methods = get_class_methods(get_class($this));
        foreach($methods as $method){
            if(!preg_match('/v\d{1,3}_\d{1,2}_\d{1,2}$/',$method)){
                continue;
            }
            $version_method =str_replace(['v','_'],['','.'],$method);
            if(version_compare($version, $version_method, '<=')){
                $this->$method();
            }
        }

    }

    protected function getContext()
    {
        return $this->context;
    }

    protected function getSetup()
    {
        return $this->setup;
    }

    protected function getInstaller()
    {
        return $this->installer;
    }

    protected function getConnection()
    {
        return $this->getInstaller()->getConnection();
    }

}
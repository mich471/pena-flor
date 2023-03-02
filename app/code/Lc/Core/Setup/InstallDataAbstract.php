<?php

namespace Lc\Core\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallSchemaAbstract
 * @description: Esta clase abstrae los simples pasos de cualquier setup, solo es necesario extender de esta clase y crear
 * un metodo con la siguiente nomenclatura: para la version 1.0.0 corresponde nombrar el metodo v1_0_0().
 * @package Lc\Core\Setup
 */
abstract class InstallDataAbstract implements InstallDataInterface
{
    /** @var EavSetup */
    protected $eavSetupFactory;

    /** @var  ModuleDataSetupInterface */
    protected $installer;

    /** @var  ModuleContextInterface */
    protected $context;

    /** @var  ModuleDataSetupInterface */
    protected $setup;


    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
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
            $version_method = str_replace(['v','_'],['','.'],$method);
            if(version_compare($version, $version_method, '<=')){
                $this->$method();
            }
        }

    }
    protected function addAttributeProduct($code, $attributes)
    {
        $this->getEavSetup()->addAttribute(\Magento\Catalog\Model\Product::ENTITY,$code, $attributes);
        return $this;
    }

    /**
     * @return EavSetup
     */
    protected function getEavSetup()
    {

        $setup =  $this->getSetup();
       return  $this->eavSetupFactory->create(['setup' => $setup]);

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
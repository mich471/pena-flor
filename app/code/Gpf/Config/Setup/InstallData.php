<?php

namespace Gpf\Config\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Customer\Model\Attribute\Backend\Data\Boolean;
// use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Model\Config;


class InstallData implements InstallDataInterface
{

    /**
     * @var Config $eavConfig
     */
    protected $eavConfig;

    /**
     * InstallData constructor.
     *
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        /** update attribute to visible in form */
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'firstname');
        $frontendClass = $attribute->getData('frontend_class');
        $attribute->addData([
            'frontend_class' => $frontendClass . ' validate-length maximum-length-20',
        ]);
    
        $attribute->save();

        $setup->endSetup();
    }
}
<?php

namespace Lyracons\CustomerActivation\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Customer\Model\Attribute\Backend\Data\Boolean;
use Magento\Customer\Setup\CustomerSetup;


class InstallData implements InstallDataInterface
{

    const CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE = 'customeractivation_is_active';

    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    /**
     * InstallData constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var $customerSetup CustomerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $attributes = [
            self::CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE        => [
                'type'         => 'int',
                'label'        => 'Active',
                'input'        => 'select',
                'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'default'      => 0,
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 998,
                'system'       => 0,
                'global'       => ScopedAttributeInterface::SCOPE_STORE,
            ]
        ];

        $customerEntityTypeId = $customerSetup->getEntityType(Customer::ENTITY, 'entity_type_id');
        $attributeSetId = $customerSetup->getEntityType(Customer::ENTITY, 'default_attribute_set_id');

        foreach ($attributes as $code => $data) {
            $customerSetup->addAttribute(Customer::ENTITY, $code, $data);
        }

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /** update attribute to visible in form */
        $activeAttribute = $customerSetup->getAttribute(Customer::ENTITY, self::CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE);
        $activeAttribute['attribute_set_id']      = $attributeSetId;
        $activeAttribute['attribute_group_id']    = $attributeGroupId;
        $activeAttribute['used_in_forms']         = ['adminhtml_customer'];
        $activeAttribute['is_used_in_grid']       = 1;
        $activeAttribute['is_visible_in_grid']    = 1;
        $activeAttribute['is_filterable_in_grid'] = 1;

        $customerSetup->updateAttribute($customerEntityTypeId, $activeAttribute['attribute_id'], $activeAttribute);

        $setup->endSetup();
    }
}

<?php
namespace Lyracons\CustomerActivation\Setup;


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Customer\Model\Attribute\Backend\Data\Boolean;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Api\CustomerMetadataInterface;


class UpgradeData implements UpgradeDataInterface
{

    const CUSTOMER_ADMIN_NOTIFICATION_PENDING = 'admin_notify_pending';
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
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    private $eavSetup;

    /**
     * InstallData constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Setup\EavSetup $eavSetup
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavConfig = $eavConfig;
        $this->eavSetup = $eavSetup;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @todo refactor method so that it respects PHPMD threshold (100) for method length
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            /** @var $customerSetup CustomerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $attributes = [
                self::CUSTOMER_ADMIN_NOTIFICATION_PENDING => [
                    'type' => 'int',
                    'label' => 'Pending Notification',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'default' => 0,
                    'required' => false,
                    'visible' => true,
                    'user_defined' => true,
                    'position' => 998,
                    'system' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                ]
            ];

            $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            foreach ($attributes as $code => $data) {
                $customerSetup->addAttribute(Customer::ENTITY, $code, $data);
            }
            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            /** update attribute to visible in form */
            $activeAttribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, self::CUSTOMER_ADMIN_NOTIFICATION_PENDING);
            $activeAttribute->addData(
                [
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer'],
                ]
            );
            $activeAttribute->save();
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            /** @var $customerSetup CustomerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, self::CUSTOMER_ADMIN_NOTIFICATION_PENDING);
            $attribute->addData(
                [
                    'default' => 0,
                ]
            );
            $attribute->save();

            $attribute = $customerSetup->getEavConfig()
                ->getAttribute(Customer::ENTITY, self::CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE);
            $attribute->addData(
                [
                    'default' => 0,
                ]
            );
            $attribute->save();
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $customerAttribute = $this->eavConfig->getAttribute(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                self::CUSTOMER_ADMIN_NOTIFICATION_PENDING
            );
            $customerAttribute->delete();
            $customerAttribute = $this->eavConfig->getAttribute(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                self::CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE
            );
            $customerAttribute->delete();

            $attributes = [
                self::CUSTOMER_ADMIN_NOTIFICATION_PENDING => [
                    'label' => 'Pending Notification',
                    'type' => 'varchar',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible' => true,
                    'required' => false,
                    'position' => 150,
                    'sort_order' => 150,
                    'default' => 0,
                    'system' => false
                ],
                self::CUSTOMER_ATTRIBUTE_ACCOUNT_ACTIVE => [
                    'label' => 'Active',
                    'type' => 'varchar',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'visible' => true,
                    'required' => false,
                    'position' => 150,
                    'sort_order' => 150,
                    'default' => 0,
                    'system' => false
                ]
            ];

            foreach ($attributes as  $code => $config) {
                /** CUSTOMER ATTRIBUTE */
                $this->eavSetup->addAttribute(
                    CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                    $code,
                    $config
                );
                $customerAttribute = $this->eavConfig->getAttribute(
                    CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                    $code
                );
                $customerAttribute->setData('used_in_forms', ['adminhtml_customer'])->save();
            }
        }
        $setup->endSetup();
    }
}

<?php

namespace Gpf\Config\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 *
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * InstallData constructor.
     *
     * @param Config $eavConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $eavConfig,
        LoggerInterface $logger
    )
    {
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            foreach ($this->getAttributesToUpdateValidation() as $data) {
                try {
                    $attribute = $this->eavConfig->getAttribute($data['entity'], $data['code']);
                    $attribute->setData('frontend_class', '');
                    $attribute->addData($data['data']);
                    $attribute->setValidateRules($data['validate_rules']);
                    $attribute->save();
                } catch (\Exception $e) {
                    $this->logger->debug($e->getTraceAsString());
                }
            }
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            try {
                $attribute = $this->eavConfig->getAttribute('customer_address', 'company');
                $attribute->addData(['is_visible' => 0]);
                $attribute->save();
            } catch (\Exception $e) {
                $this->logger->debug($e->getTraceAsString());
            }
        }
        $setup->endSetup();
    }


    /**
     * @return array
     */
    protected function getAttributesToUpdateValidation()
    {
        return [
            [
                'entity' => Customer::ENTITY,
                'code' => 'firstname',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-20 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 20,
                ]
            ],
            [
                'entity' => Customer::ENTITY,
                'code' => 'lastname',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-20 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 20,
                ]
            ],
            [
                'entity' => Customer::ENTITY,
                'code' => 'document_id',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-40 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 40,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'firstname',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-20 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 20,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'lastname',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-20 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 20,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'street',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-40 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 40,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'city',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-25 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 25,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'postcode',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-12 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 12,
                ]
            ],
            [
                'entity' => 'customer_address',
                'code' => 'telephone',
                'data' => [
                    'frontend_class' => ' validate-length maximum-length-30 ',
                ],
                'validate_rules' => [
                    'min_text_length' => 1,
                    'max_text_length' => 30,
                ]
            ],
        ];
    }
}

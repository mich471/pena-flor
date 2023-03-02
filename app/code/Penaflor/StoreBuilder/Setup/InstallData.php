<?php

namespace Penaflor\StoreBuilder\Setup;

/**
 * Class InstallData
 * @package Penaflor\StoreBuilder\Setup
 */
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var GroupFactory
     */
    private $groupFactory;
    /**
     * @var Group
     */
    private $groupResourceModel;
    /**
     * @var StoreFactory
     */
    private $storeFactory;
    /**
     * @var Store
     */
    private $storeResourceModel;
    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var Website
     */
    private $websiteResourceModel;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * InstallData constructor.
     * @param WebsiteFactory $websiteFactory
     * @param Website $websiteResourceModel
     * @param Store $storeResourceModel
     * @param Group $groupResourceModel
     * @param StoreFactory $storeFactory
     * @param GroupFactory $groupFactory
     * @param ManagerInterface $eventManager
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     */
    public function __construct(
        Group $groupResourceModel,
        GroupFactory $groupFactory,
        ManagerInterface $eventManager,
        Store $storeResourceModel,
        StoreFactory $storeFactory,
        Website $websiteResourceModel,
        WebsiteFactory $websiteFactory,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter
    ) {
        $this->eventManager = $eventManager;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->storeFactory = $storeFactory;
        $this->storeResourceModel = $storeResourceModel;
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        try {
            $this->createStore(
                [
                    'website_code' => 'penaflor',
                    'website_name' => 'Grupo Peñaflor Website',
                    'group_code' => 'penaflor',
                    'group_name' => 'Grupo Peñaflor Store',
                    'store_code' => 'penaflor',
                    'store_name' => 'Grupo Penaflor Store View',
                    'unsecure_url' => str_replace('www.', 'www.penaflor.', $this->getBaseUnsecureUrl()),
                    'secure_url' => str_replace('www.', 'www.penaflor.', $this->getBaseSecureUrl()),
                    'logo' => '',
                    'is_active' => '1',
                    'theme_id' => '19',
                    'currency' => 'ARS',
                    'locale' => 'es_AR',
                ]
            );
        } catch (\Exception $e){

        }

        $setup->endSetup();
    }

    /**
     * @param $data
     * @return $this
     * @throws \Exception
     */
    public function createStore($data)
    {
        /** @var  \Magento\Store\Model\Store $store */
        $store = $this->storeFactory->create();
        // $store->load($data['store_code']);
        // if (!$store->getId()) {
        /** @var \Magento\Store\Model\Website $website */
        $website = $this->websiteFactory->create();
        $website->load($data['website_code']);
        $website = $this->setWebId($website, $data);

        /** @var \Magento\Store\Model\Group $group */
        $group = $this->groupFactory->create();
        $group->setWebsiteId($website->getWebsiteId());
        $group->setName($data['group_name']);
        $group->setCode($data['group_code']);
        $group->save();

        $group = $this->groupFactory->create();
        $group->load($data['group_name'], 'name');

        $store->setCode($data['store_code']);
        $store->setName($data['store_name']);
        $store->setWebsite($website);
        $store->setGroupId($group->getId());
        $store->setData('is_active', $data['is_active']);
        $store->save();

        $this->eventManager->dispatch('store_add', ['store' => $store]);

        $config = $this->getConfig($data);
        $this->saveConfig($store->getId(), $config, 'stores');
        $this->saveConfig($website->getId(), $config, 'websites');

        // }
        return $this;
    }

    /**
     * @param int $id
     * @param array $config
     * @param string $scope
     * @return $this
     */
    protected function saveConfig($id, $config, $scope)
    {
        if (!isset($config[$scope])) {
            return $this;
        }

        foreach ($config[$scope] as $path => $value) {
            $this->configWriter->save($path, $value, $scope, $id);
        }

        return $this;
    }

    /**
     * @param \Magento\Store\Model\Website $website
     * @param array $data
     * @return \Magento\Store\Model\Website
     * @throws \Exception
     */
    protected function setWebId($website, $data)
    {
        if (!$website->getId()) {
            $website->setCode($data['website_code']);
            $website->setName($data['website_name']);
            $website->save();
        }

        return $website;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getConfig(array $data)
    {
        return [
            // 'stores' =>[
            //     // Web
            //     'web/unsecure/base_url'        => $data['unsecure_url'],
            //     'web/secure/base_url'          => $data['secure_url'],
            //     'web/unsecure/base_link_url'   => $data['unsecure_url'],
            //     'web/secure/base_link_url'     => $data['secure_url'],
            // ],
            'websites' => [
                'design/theme/theme_id' => $data['theme_id'],
                'design/head/includes' => null,
                'design/head/title_prefix' => null,
                'design/head/title_suffix' => null,
                'design/email/logo_alt' => $data['logo'],
                'design/email/logo_height' => $data['logo'],
                'design/email/logo_width' => $data['logo'],
                'design/header/logo_height' => $data['logo'],
                'design/header/logo_width' => $data['logo'],
                'currency/options/base' => $data['currency'],
                'currency/options/default' => $data['currency'],
                'currency/options/allow' => $data['currency'],
                'general/locale/code' => $data['locale'],
                // Web
                'web/unsecure/base_url' => $data['unsecure_url'],
                'web/secure/base_url' => $data['secure_url'],
                'web/unsecure/base_link_url' => $data['unsecure_url'],
                'web/secure/base_link_url' => $data['secure_url'],
            ],
        ];
    }

    /**
     * @return string
     */
    protected function getBaseSecureUrl()
    {
        return $this->scopeConfig->getValue('web/unsecure/base_url', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return string
     */
    protected function getBaseUnsecureUrl()
    {
        return $this->scopeConfig->getValue('web/secure/base_url', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}

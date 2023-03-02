<?php

namespace Lyracons\CustomerActivation\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_CONFIG_PATH_MODULE_ENABLE = 'customer/lyracons_customeractivation/enabled';
    const XML_CONFIG_PATH_EMAIL_TEMPLATE = 'customer/lyracons_customeractivation/template';
    const XML_CONFIG_PATH_ADMIN_NOTIFY_ON_REGISTER = 'customer/lyracons_customeractivation/notify_on_register';
    const XML_CONFIG_PATH_ADMIN_NOTIFY_TO = 'customer/lyracons_customeractivation/notify_to_admin';
    const XML_CONFIG_PATH_ADMIN_EMAIL_TEMPLATE = 'customer/lyracons_customeractivation/template_admin';

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;


    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isModuleActive($storeId = null)
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_ENABLE, $storeId);
    }

    /**
     * @return string
     */
    public function getCustomerEmailTemplatePath()
    {
        return self::XML_CONFIG_PATH_EMAIL_TEMPLATE;
    }

    /**
     * @return string
     */
    public function getAdminEmailTemplatePath()
    {
        return self::XML_CONFIG_PATH_ADMIN_EMAIL_TEMPLATE;
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getAdminNotifyOnRegister($storeId = null)
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_ADMIN_NOTIFY_ON_REGISTER, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getAdminNotifyTo($storeId = null)
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_ADMIN_NOTIFY_TO, $storeId);
    }

    public function getSuccessRedirectPath()
    {
        return "/";
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
         */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

<?php

namespace Gpf\CustomerLogger\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;


class Data extends AbstractHelper
{
    const XML_CONFIG_PATH_MODULE_ENABLE = 'customer/gpf_customerlogger/enabled';

    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_ENABLE, $storeId);
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
     *      */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
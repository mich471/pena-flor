<?php

namespace SummaSolutions\WoowUp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Configuration extends AbstractHelper
{
    const PUBLIC_KEY_PATH = "web/woowup_traking/public_key";

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->scopeConfig->getValue(self::PUBLIC_KEY_PATH, ScopeInterface::SCOPE_WEBSITE);
    }
}

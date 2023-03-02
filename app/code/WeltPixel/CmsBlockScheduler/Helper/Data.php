<?php

namespace WeltPixel\CmsBlockScheduler\Helper;

/**
 * Helper Data
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH  = 'weltpixel_cmsblockscheduler_config/general/';

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
    }

    public function resourceEnabled($type) // tag | date_range | customer_group
    {
        $sysPath = self::CONFIG_PATH . $type;

        return $this->_scopeConfig->getValue($sysPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

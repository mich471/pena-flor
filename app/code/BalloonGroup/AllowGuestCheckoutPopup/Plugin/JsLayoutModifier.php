<?php

namespace BalloonGroup\AllowGuestCheckoutPopup\Plugin;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Block\Account\AuthenticationPopup;

class JsLayoutModifier
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Json $json
     * @param ScopeConfigInterface
     */
    public function __construct(
        Json $json,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string 
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {   
        $shouldModifyLayout = $this->scopeConfig->getValue(
            'guestCheckoutPopup/guestCheckoutPopup/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if($shouldModifyLayout) {
            $layout = $this->json->unserialize($result);
            $layout['components']['authenticationPopup']['component'] = 'BalloonGroup_AllowGuestCheckoutPopup/js/view/authentication-popup';
            return $this->json->serialize($layout);
        }

        return $result;
    }
}
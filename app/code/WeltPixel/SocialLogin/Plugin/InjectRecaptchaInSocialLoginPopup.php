<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Plugin;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;


/**
 * Class InserRecaptchaInSocialLogin
 * @package WeltPixel\SocialLogin\Plugin
 */
class InjectRecaptchaInSocialLoginPopup
{
    /**
     * @var UiConfigResolverInterface
     */
    private $captchaUiConfigResolver;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param Json $serializer
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        Json $serializer
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->serializer = $serializer;
    }

    /**
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        $layout = $this->serializer->unserialize($result);
        $loginKey = 'customer_login';
        $crateAccountKey = 'customer_create';

        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($loginKey)) {
            $layout['components']['ajaxLogin']['children']['recaptcha-login']['settings']
                = $this->captchaUiConfigResolver->get($loginKey);
        } else {
            unset($layout['components']['ajaxLogin']['children']['recaptcha-login']);
        }

        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($crateAccountKey)) {
            $layout['components']['ajaxLogin']['children']['recaptcha-register']['settings']
                = $this->captchaUiConfigResolver->get($crateAccountKey);
        } else {
            unset($layout['components']['ajaxLogin']['children']['recaptcha-register']);
        }

        return $this->serializer->serialize($layout);
    }
}

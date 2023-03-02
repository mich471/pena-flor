<?php

namespace WeltPixel\Newsletter\Plugin\Captcha;

use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

class ConfigResolver
{
    /**
     * @param UiConfigResolverInterface $subject
     * @param string $key
     * @return string[]
     */
    public function beforeGet(
        UiConfigResolverInterface $subject,
        string $key
    ) {
        if (in_array($key, ['wpn-recaptcha-newsletter','wpn-recaptcha-newsletter-exitintnt'])) {
            $key = 'newsletter';
        }

        return [$key];
    }
}

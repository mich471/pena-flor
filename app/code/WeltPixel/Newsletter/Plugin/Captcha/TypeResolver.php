<?php

namespace WeltPixel\Newsletter\Plugin\Captcha;

use Magento\ReCaptchaUi\Model\CaptchaTypeResolverInterface;

class TypeResolver
{
    /**
     * @param CaptchaTypeResolverInterface $subject
     * @param string $key
     * @return string[]
     */
    public function beforeGetCaptchaTypeFor(
        CaptchaTypeResolverInterface $subject,
        string $key
    ) {
        if (in_array($key, ['wpn-recaptcha-newsletter','wpn-recaptcha-newsletter-exitintnt'])) {
            $key = 'newsletter';
        }

        return [$key];
    }
}

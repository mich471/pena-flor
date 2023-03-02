<?php

namespace WeltPixel\FrontendOptions\Plugin\Captcha;

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
        if (in_array($key, ['wpn-recaptcha-newsletter-pearl'])) {
            $key = 'newsletter';
        }

        return [$key];
    }
}

<?php

namespace WeltPixel\FrontendOptions\Plugin\Captcha;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;

class IsCaptchaEnabled
{
    /**
     * @param IsCaptchaEnabledInterface $subject
     * @param string $key
     * @return string[]
     */
    public function beforeIsCaptchaEnabledFor(
        IsCaptchaEnabledInterface $subject,
        string $key
    ) {
        if (in_array($key, ['wpn-recaptcha-newsletter-pearl'])) {
            $key = 'newsletter';
        }

        return [$key];
    }
}

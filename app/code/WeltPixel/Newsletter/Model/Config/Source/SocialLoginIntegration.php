<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SocialLoginIntegration
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class SocialLoginIntegration implements ArrayInterface
{
    const EMAIL_ONLY = 1;
    const SOCIALLOGIN_ONLY = 2;
    const EMAIL_AND_SOCIALLOGIN = 3;

    /**
     * Return list of Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::EMAIL_ONLY,
                'label' => __('Email Only')
            ),
            array(
                'value' => self::SOCIALLOGIN_ONLY,
                'label' => __('Social Login Only')
            ),
            array(
                'value' => self::EMAIL_AND_SOCIALLOGIN,
                'label' => __('Email + Social Login')
            )
        );
    }
}
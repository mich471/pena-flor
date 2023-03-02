<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SocialLoginIntegrationApplyTo
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class SocialLoginIntegrationApplyTo implements ArrayInterface
{
    const POPUP_ONLY = 1;
    const EXIT_INTENT_ONLY = 2;
    const BOTH = 3;

    /**
     * Return list of Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::POPUP_ONLY,
                'label' => __('Newsletter Popup Only')
            ),
            array(
                'value' => self::EXIT_INTENT_ONLY,
                'label' => __('Exit Intent Only')
            ),
            array(
                'value' => self::BOTH,
                'label' => __('Both')
            )
        );
    }
}
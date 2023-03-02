<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class VisitorCounterSessionIdentifier
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class VisitorCounterSessionIdentifier implements ArrayInterface
{
    const IDENTIFIER_SESSION = 'session';
    const IDENTIFIER_IP = 'ip';

    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::IDENTIFIER_SESSION,
                'label' => __('User Session')
            ),
            array(
                'value' => self::IDENTIFIER_IP,
                'label' => __('User Ip')
            )
        );
    }
}

<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PopupStyle
 *
 * @package WeltPixel\SocialLogin\Model\Config\Source
 */
class PopupStyle implements ArrayInterface
{

    const TYPE_DEFAULT = 'popup';
    const TYPE_SLIDE_RIGHT = 'slide';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::TYPE_DEFAULT,
                'label' => __('Default ( Social Login popup displayed in the center of the page )'),
            ),
            array(
                'value' => self::TYPE_SLIDE_RIGHT,
                'label' => __('Right Side ( Social Login popup Slides in the right side of the page )'),
            )
        );
    }
}
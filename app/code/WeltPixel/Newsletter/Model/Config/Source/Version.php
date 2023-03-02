<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
/**
 * Class Version
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class Version implements ArrayInterface
{
    const VERSION_1 = 1;
    const VERSION_2 = 2;
    const VERSION_3 = 3;
    const VERSION_4 = 4;

    /**
     * Return list of Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::VERSION_1,
                'label' => __('Version 1')
            ),
            array(
                'value' => self::VERSION_2,
                'label' => __('Version 2')
            ),
            array(
                'value' => self::VERSION_3,
                'label' => __('Version 3')
            ),
            array(
                'value' => self::VERSION_4,
                'label' => __('Version 4')
            )
        );
    }
}
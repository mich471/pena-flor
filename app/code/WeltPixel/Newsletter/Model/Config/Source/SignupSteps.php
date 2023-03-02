<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
/**
 * Class SignupSteps
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class SignupSteps implements ArrayInterface
{
    const STEP_1 = 1;
    const STEP_2 = 2;

    /**
     * Return list of Versions
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::STEP_1,
                'label' => __('One Step')
            ),
            array(
                'value' => self::STEP_2,
                'label' => __('Two Steps')
            )
        );
    }
}
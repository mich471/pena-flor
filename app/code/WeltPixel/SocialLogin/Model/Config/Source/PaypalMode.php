<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PaypalMode
 * @package WeltPixel\SocialLogin\Model\Config\Source
 */
class PaypalMode
{
    /**
     * @var array
     */
    protected $_modes = array(
        '1' => 'Sandbox Mode',
        '0' => 'Live Mode',
    );
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_modes as $id => $label) :
            $options[] = array(
                'value' => $id,
                'label' => $label
            );
        endforeach;
        return $options;
    }
}
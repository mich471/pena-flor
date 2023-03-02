<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\Config\Source;


/**
 * Class CartStyle
 * @package WeltPixel\SocialLogin\Model\Config\Source
 */
class CartStyle
{
    /**
     * @var array
     */
    protected $_modes = array(
        '1' => 'Opened by default',
        '0' => 'Closed by default ',
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
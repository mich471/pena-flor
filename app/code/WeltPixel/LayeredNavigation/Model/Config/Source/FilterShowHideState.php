<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FilterShowHideState
 * @package WeltPixel\LayeredNavigation\Model\Config\Source
 */
class FilterShowHideState implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_styles = [
        'open' => 'Open',
        'closed' => 'Closed',
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_styles as $id => $style) :
            $options[] = [
                'value' => $id,
                'label' => $style
            ];
        endforeach;
        return $options;
    }
}

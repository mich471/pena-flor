<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_RecentlyViewedBar
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\RecentlyViewedBar\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowAttributes
 * @package WeltPixel\RecentlyViewedBar\Model\Config\Source
 */
class ShowAttributes implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_options = array(
        '1' => 'Product Image',
        '2' => 'Product Name',
        '3' => 'Product Price',
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_options as $id => $option) :
            $options[] = array(
                'value' => $id,
                'label' => $option
            );
        endforeach;
        return $options;
    }
}

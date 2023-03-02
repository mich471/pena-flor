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
 * Class ShowButtons
 * @package WeltPixel\RecentlyViewedBar\Model\Config\Source
 */
class ShowButtons implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_options = array(
        '' => 'None',
        '1' => 'Add To Cart',
        '2' => 'Add To Compare',
        '3' => 'Add To Wishlist',
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

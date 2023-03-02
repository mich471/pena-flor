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
 * Class DisplayOn
 * @package WeltPixel\RecentlyViewedBar\Model\Config\Source
 */
class DisplayOn implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_styles = array(
        '1' => 'All Pages (excluded: checkout & cart & my account & login)',
        '2' => 'Category',
        '3' => 'Product',
        '4' => 'CMS'
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_styles as $id => $style) :
            $options[] = array(
                'value' => $id,
                'label' => $style
            );
        endforeach;
        return $options;
    }
}
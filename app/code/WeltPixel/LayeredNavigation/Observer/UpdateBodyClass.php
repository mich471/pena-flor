<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class UpdateBodyClass
 * @package WeltPixel\LayeredNavigation\Observer
 */
class UpdateBodyClass implements ObserverInterface
{
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * RemoveBlocks constructor.
     * @param \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
     */
    public function __construct(
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
    ) {
        $this->_wpHelper = $wpHelper;
    }

    public function execute(Observer $observer)
    {
        $this->_wpHelper->updateSliderBodyClass();
    }
}
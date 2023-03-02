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
 * Class SidebarStyle
 * @package WeltPixel\RecentlyViewedBar\Model\Config\Source
 */
class CmsBlock implements ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\Category\Attribute\Source\Page
     */
    protected $staticblocks;

    /**
     * CmsBlock constructor.
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Page $staticblocks
     */
    public function __construct(
        \Magento\Catalog\Model\Category\Attribute\Source\Page $staticblocks
    )
    {
        $this->staticblocks = $staticblocks;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $staticBlocks = $this->staticblocks->getAllOptions();
        return $staticBlocks;
    }


}
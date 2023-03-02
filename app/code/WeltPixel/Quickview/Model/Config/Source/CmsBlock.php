<?php

namespace WeltPixel\Quickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CmsBlock
 * @package WeltPixel\Quickview\Model\Config\Source
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
    ) {
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

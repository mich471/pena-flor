<?php

namespace WeltPixel\SmartProductTabs\Block;

use Magento\Catalog\Block\Product\View\Attributes;

/**
 * Class DynamicSmartProductTabs
 * @package WeltPixel\SmartProductTabs\Block
 */
class DynamicSmartProductTabs extends Attributes
{
    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $content = $this->getData('content');
        if ($content) {
            $html = $content;
        }
        return $html;
    }
}

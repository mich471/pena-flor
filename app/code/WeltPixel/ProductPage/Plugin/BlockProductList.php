<?php

namespace WeltPixel\ProductPage\Plugin;

/**
 * Class BlockProductList
 * @package WeltPixel\ProductPage\Plugin
 */
class BlockProductList
{
    /**
     * @var \WeltPixel\ProductPage\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \WeltPixel\ProductPage\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\ProductPage\Helper\Data $helper
        ) {
        $this->helper = $helper;
    }

    /**
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param bool $result
     * @return bool
     */
    public function afterGetAdditionalHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if ($this->helper->isPrevNextEnabled()) {
            $result .= $subject->getChildHtml('categorysearch.productprevnext.additional');
        }

        return $result;
    }
}

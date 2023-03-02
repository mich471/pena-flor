<?php

namespace WeltPixel\ProductLabels\Plugin\Widget;

class ProductList
{

    /**
     * @var \WeltPixel\ProductLabels\Helper\Data
     */
    protected $productLabelsHelper;

    /**
     * ProductList constructor.
     * @param \WeltPixel\ProductLabels\Helper\Data $productLabelsHelper
     */
    public function __construct(
        \WeltPixel\ProductLabels\Helper\Data $productLabelsHelper
    ) {
        $this->productLabelsHelper = $productLabelsHelper;
    }

    /**
     * @param \Magento\CatalogWidget\Block\Product\ProductsList $subject
     * @param string $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\CatalogWidget\Block\Product\ProductsList $subject,
        $result
    ) {
        if ($this->productLabelsHelper->enableForProductWidgets()) {
            $result = 'WeltPixel_ProductLabels::product/widget/content/grid.phtml';
        }

        return $result;
    }
}

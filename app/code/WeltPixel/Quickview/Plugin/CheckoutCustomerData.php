<?php

namespace WeltPixel\Quickview\Plugin;

class CheckoutCustomerData
{
    /**
     * @var \WeltPixel\Quickview\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \WeltPixel\Quickview\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\Quickview\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\CustomerData\ItemInterface $subject
     * @param $result
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    public function afterGetItemData(
        \Magento\Checkout\CustomerData\ItemInterface $subject,
        $result,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        if (!$this->helper->isAjaxCartEnabled()) {
            return $result;
        }

        $result = \array_merge(
            ['product_initial_price' => $item->getProduct()->getPrice()],
            ['product_final_price' => $item->getProduct()->getFinalPrice()],
            $result
        );

        return $result;
    }
}

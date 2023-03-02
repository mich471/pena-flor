<?php

namespace SummaSolutions\GetVarietal\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Framework\Escaper;
use Magento\Quote\Model\Quote\Item;

class DefaultItem
{
    protected Escaper $escaper;

    public function __construct
    (
        Escaper $escaper
    )
    {
        $this->escaper = $escaper;
    }

    public function aroundGetItemData(
        AbstractItem $subject,
        \Closure $proceed,
        Item $item
    ) {
        $data = $proceed($item);
        $productName = $this->escaper->escapeHtml($item->getProduct()->getName());
        $result['varietal'] = $item->getProduct()->getAttributeText('varietal');
        return \array_merge(
            $result,
            $data
        );
    }
}
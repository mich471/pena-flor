<?php

namespace Penaflor\Checkout\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\DefaultItem as CoreDefaultItem;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\Product;

/**
 * Class DefaultItem
 * @package Penaflor\Checkout\Plugin\Checkout\CustomerData
 */
class DefaultItem
{
    /**
     * @var Item
     */
    protected $item;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @param CoreDefaultItem $subject
     * @param callable $proceed
     * @param Item $item
     * @return mixed
     */
    public function aroundGetItemData(CoreDefaultItem $subject, callable $proceed, Item $item)
    {
        $result = $proceed($item);
        $this->product = $item->getProduct();
        $this->item = $item;
        $bodega = $this->product->hasData('bodega') ? $this->product->getAttributeText("bodega") : '';
        if (in_array($bodega, ['No', null, false, 'false'])) {
            $bodega = '';
        }
        $data = [
            'custom_attributes' => [
                'bodega' => $bodega,
                'varietal' => $this->product->getAttributeText("varietal"),
            ]
        ];
        if ($this->hasSpecialPrice()) {
            // precio a mostrar tachado:
            $data['custom_attributes']['old_price'] = '$' . number_format($this->getRegularPrice(), 2, ',', '.');
        }

        return array_merge($result, $data);
    }

    /**
     * @return bool
     */
    private function hasSpecialPrice()
    {
        // only compare the integer part
        return round((float) $this->item->getData('price_incl_tax')) < floor((float) $this->getRegularPrice());
    }

    /**
     * @return float
     */
    private function getRegularPrice()
    {
        return $this->product
            ->getPriceInfo()
            ->getPrice(RegularPrice::PRICE_CODE)
            ->getAmount()
            ->getValue();
    }

}

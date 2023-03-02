<?php

namespace Penaflor\Checkout\Plugin\Checkout\Model;

use Magento\Checkout\Model\DefaultConfigProvider as Subject;

/**
 * Class DefaultConfigProvider
 * @package Penaflor\Checkout\Plugin\Model
 */
class DefaultConfigProvider
{
    /**
     * Product Value => Item Value
     * @var array
     */
    protected $attributesToClone = [
        'varietal_value' => 'varietal',
        'bodega_value' => 'bodega'
    ];

    /**
     * @var array
     */
    private $quoteItems = [];

    /**
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(Subject $subject, array $result)
    {
        $this->quoteItems = $result['quoteItemData'];
        $items = &$result['totalsData']['items'];

        foreach ($items as &$item) {
            // reset() may throw an exception
            // if array_filter returns an empty array
            try {
                $quoteItem = $this->getQuoteItem($item);
            } catch (\Exception $e) {
                continue;
            }
            $this->cloneAttributes($item, $quoteItem['product']);
        }

        return $result;
    }

    /**
     * @param $item
     * @param $product
     */
    private function cloneAttributes(&$item, $product)
    {
        foreach($this->attributesToClone as $productAttribute => $itemAttribute) {
            if (isset($product[$productAttribute])) {
                $item[$itemAttribute] = $product[$productAttribute];
            }
        }
    }

    /**
     * @param $item
     * @return mixed
     */
    private function getQuoteItem($item)
    {
        $quoteItem = array_filter($this->quoteItems, function ($quoteItem) use ($item) {
            return $quoteItem['item_id'] === $item['item_id'];
        });

        return reset($quoteItem);
    }
}
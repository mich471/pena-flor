<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Api\Data;

interface RateSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}

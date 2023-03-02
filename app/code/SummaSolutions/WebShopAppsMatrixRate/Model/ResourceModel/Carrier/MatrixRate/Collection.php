<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Model\ResourceModel\Carrier\MatrixRate;

use SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateSearchResultInterface;
use WebShopApps\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\Collection as MatrixRateCollection;
use Magento\Framework\Api\SearchCriteriaInterface;

class Collection
    extends MatrixRateCollection
    implements RateSearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @param array|null $items
     * @return $this|Collection
     * @throws \Exception
     */
    public function setItems(array $items = null)
    {
        if (!$items) {
            return $this;
        }
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }


    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this|Collection
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteria = $searchCriteria;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @param $totalCount
     * @return $this|Collection
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @return SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return $this->searchCriteria;
    }
}

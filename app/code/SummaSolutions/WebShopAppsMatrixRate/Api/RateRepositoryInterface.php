<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Api;

interface RateRepositoryInterface
{

    /**
     * @param int $rateId
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface
     */
    public function get($rateId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface
     */
    public function save(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate);

    /**
     * @param \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface
     */
    public function update(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate);

    /**
     * @param int $rateId
     * @return \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface
     */
    public function delete($rateId);
}

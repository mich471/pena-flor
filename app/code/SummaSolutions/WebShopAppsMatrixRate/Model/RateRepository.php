<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface;
use SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateSearchResultInterface;
use SummaSolutions\WebShopAppsMatrixRate\Api\RateRepositoryInterface;
use SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterfaceFactory;
use WebShopApps\MatrixRate\Model\ResourceModel\Carrier\Matrixrate;
use WebShopApps\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\CollectionFactory;
use SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class RateRepository implements RateRepositoryInterface
{
    const NEEDED_FOR_UPDATE_PRICE = [
        'website_id',
        'dest_country_id',
        'dest_region_id',
        'dest_city',
        'dest_zip',
        'condition_name',
        'condition_from_value',
        'condition_to_value',
        'shipping_method'
    ];

    /**
     * @var RateInterfaceFactory
     */
    protected RateInterfaceFactory $rateFactory;
    /**
     * @var Matrixrate
     */
    protected Matrixrate $rateResource;
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;
    /**
     * @var SearchResultFactory
     */
    protected SearchResultFactory $searchResultFactory;
    /**
     * @var CollectionProcessorInterface
     */
    protected CollectionProcessorInterface $collectionProcessor;

    /**
     * @param Matrixrate $rateResource
     * @param RateInterfaceFactory $rateFactory
     * @param CollectionFactory $collectionFactory
     * @param SearchResultFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \WebShopApps\MatrixRate\Model\ResourceModel\Carrier\Matrixrate $rateResource,
        RateInterfaceFactory $rateFactory,
        CollectionFactory $collectionFactory,
        SearchResultFactory $searchResultFactory,
        CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->rateFactory = $rateFactory;
        $this->rateResource = $rateResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param $rateId
     * @return RateInterface
     * @throws NoSuchEntityException
     */
    public function get($rateId)
    {
        $rate = $this->rateFactory->create();

        if ($rateId) {
            $this->rateResource->load($rate, $rateId);
        }
        $this->rateResource->load($rate, $rateId);

        if (!$rate->getId()) {
            throw new NoSuchEntityException(
                __(
                    "The rate isn't available."
                )
            );
        }

        return $rate;
    }

    /**
     * @param RateInterface $rate
     * @return RateInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function save(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate)
    {
        try {
            $this->rateResource->save($rate);
        } catch (AlreadyExistsException $e) {
            throw new LocalizedException(
                __(
                    "Rate already exists"
                )
            );
        }

        return $this->get($rate->getId());
    }

    /**
     * @param RateInterface $rate
     * @return RateInterface
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException|LocalizedException
     */
    public function update(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate)
    {
        if ($rate->getId()) {
           $oldRate = $this->get($rate->getId());
        } else {
            return $this->updatePrice($rate);
        }
        if (!$oldRate->getId()) {
            throw new NoSuchEntityException(
                __(
                    "Didn't find rate to update, maybe resource is new? "
                )
            );
        }
        $oldRate->setData($rate->getData());
        $this->rateResource->save($oldRate);

        return $this->get($oldRate->getId());
    }

    /**
     * @param $rateId
     * @return RateInterface
     * @throws Exception
     */
    public function delete($rateId)
    {
        $rate = $this->rateFactory->create();
        $this->rateResource->load($rate, $rateId);
        $this->rateResource->delete($rate);

        return $rate;
    }

    /**
     * @param RateInterface $rate
     * @return DataObject
     * @throws LocalizedException
     */
    private function getByConstraint(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate)
    {
        $collection = $this->collectionFactory->create();

        $this->validateRateForSearch($rate);

        $collection->addFieldToFilter('website_id', ['eq' => $rate->getWebsiteId()])
            ->addFieldToFilter('dest_country_id', ['eq' => $rate->getDestCountryId()])
            ->addFieldToFilter('dest_region_id', ['eq' => $rate->getDestRegionId()])
            ->addFieldToFilter('dest_city', ['eq' => $rate->getDestCity()])
            ->addFieldToFilter('dest_zip', ['eq' => $rate->getDestZip()])
            ->addFieldToFilter('condition_name', ['eq' => $rate->getConditionName()])
            ->addFieldToFilter('condition_from_value', ['eq' => $rate->getConditionFromValue()])
            ->addFieldToFilter('condition_to_value', ['eq' => $rate->getConditionToValue()])
            ->addFieldToFilter('shipping_method', ['eq' => $rate->getShippingMethod()]);

        return $collection->getFirstItem();
    }

    /**
     * @param RateInterface $rate
     * @return RateInterface
     * @throws LocalizedException
     */
    private function validateRateForSearch(\SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface $rate)
    {
        $errorAttributes = [];

        foreach (self::NEEDED_FOR_UPDATE_PRICE as $attribute) {
            if ($rate->getData($attribute) === null) {
                $errorAttributes[] = $attribute;
            }
        }

        if (!empty($errorAttributes)) {
            throw new LocalizedException(
                __(
                    "Missing following attributes to update price: "
                    . implode(', ', $errorAttributes)
                    . " to update attributes other than price, pk is needed"
                )
            );
        }

        return $rate;
    }

    /**
     * @param RateInterface $rate
     * @return RateInterface
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function updatePrice(RateInterface $rate)
    {
        $oldRate = $this->get($this->getByConstraint($rate)->getPk());
        $oldRate->setPrice($rate->getPrice());
        $oldRate->setCost($rate->getCost());
        $this->rateResource->save($oldRate);

        return $this->get($oldRate->getId());

    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return RateSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RateSearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();

        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}

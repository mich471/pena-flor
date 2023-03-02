<?php
/**
 * Created by PhpStorm.
 * User: jgimenez
 * Date: 05/10/2017
 * Time: 09:25
 */

namespace Lc\Core\Model;

use Lc\Core\Api\RepositoryInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\ObjectManagerInterface;
use Lc\Core\Model\ResourceModel\Entity\AbstractCollection;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Psr\Log\LoggerInterface;


abstract class AbstractRepository implements RepositoryInterface
{

    /**
     * @var ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $registry = [];

    protected $entityFactory;

    protected $resourceCollectionFactory;

    protected $searchResult;


    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var string $dataObjectType
     */
    protected $dataObjectType;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * AbstractRepository constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        LoggerInterface $logger
    )
    {
        $this->objectManager = $objectManager;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->logger = $logger;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return ExtensibleDataInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('Id required'));
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->entityFactory->create()->load($id);
            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(__('Requested entity doesn\'t exist'));
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->resourceCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);

        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        return $this->deleteById($entity->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        $entity->delete();
        return true;
    }

    /**
     * Perform persist operations for one entity
     *
     * @param ExtensibleDataInterface $entity
     * @return ExtensibleDataInterface
     */
    public function save($entity)
    {
        $entityData = $this->extensibleDataObjectConverter->toNestedArray(
            $entity,
            [],
            $this->getDataObjectType()
        );

        $entityModel = $this->entityFactory->create(['data' => $entityData]);
        $entityModel->save();

        $this->registry[$entity->getEntityId()] = $entity;
        return $this->registry[$entity->getEntityId()];
    }

    protected function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, AbstractCollection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    protected function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, AbstractCollection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    protected function addPagingToCollection(SearchCriteriaInterface $searchCriteria, AbstractCollection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    protected function buildSearchResult(SearchCriteriaInterface $searchCriteria, AbstractCollection $collection)
    {
        $collection->setSearchCriteria($searchCriteria);
        $collection->setTotalCount($collection->getSize());
        return $collection;
    }

    /**
     * @param mixed $entityFactory
     * @return AbstractRepository
     */
    public function setEntityFactory($entityFactory)
    {
        $this->entityFactory = $entityFactory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityFactory()
    {
        return $this->entityFactory;
    }

    /**
     * @return mixed
     */
    public function getResourceCollectionFactory()
    {
        return $this->resourceCollectionFactory;
    }

    /**
     * @param mixed $resourceCollectionFactory
     * @return $this
     */
    public function setResourceCollectionFactory($resourceCollectionFactory)
    {
        $this->resourceCollectionFactory = $resourceCollectionFactory;
        return $this;
    }

    /**
     * @param mixed $searchResult
     * @return AbstractRepository
     */
    public function setSearchResult($searchResult)
    {
        $this->searchResult = $searchResult;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearchResult()
    {
        return $this->searchResult;
    }

    /**
     * @param $dataObjectType
     * @return $this
     */
    protected function setDataObjectType($dataObjectType)
    {
        $this->dataObjectType = $dataObjectType;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getDataObjectType()
    {
        return $this->dataObjectType;
    }
}
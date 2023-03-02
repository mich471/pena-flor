<?php

namespace Lc\Core\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\SearchResultInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

interface RepositoryInterface
{

    /**
     * Lists.
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return SearchResultInterface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Loads a specified object.
     * @param int $id The Used ID.
     * @return ExtensibleDataInterface
     */
    public function get($id);

    /**
     * Deletes a specified order.
     * @param ExtensibleDataInterface $entity
     * @return bool
     */
    public function delete($entity);

    /**
     * Performs persist operations for a specified.
     * @param ExtensibleDataInterface $entity
     * @return ExtensibleDataInterface
     */
    public function save($entity);

}
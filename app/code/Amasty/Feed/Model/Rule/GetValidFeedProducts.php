<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Rule;

use Amasty\Feed\Model\Feed;
use Amasty\Feed\Model\InventoryResolver;
use Amasty\Feed\Model\Rule\Condition\Sql\Builder;
use Amasty\Feed\Model\ValidProduct\ResourceModel\ValidProduct;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;

class GetValidFeedProducts
{
    public const BATCH_SIZE = 1000;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Builder
     */
    protected $sqlBuilder;

    /**
     * @var InventoryResolver
     */
    private $inventoryResolver;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        RuleFactory $ruleFactory,
        CollectionFactory $productCollectionFactory,
        Builder $sqlBuilder,
        InventoryResolver $inventoryResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->sqlBuilder = $sqlBuilder;
        $this->inventoryResolver = $inventoryResolver;
        $this->storeManager = $storeManager;
    }

    public function execute(Feed $model, array $ids = []): void
    {
        $rule = $this->ruleFactory->create();
        $rule->setConditionsSerialized($model->getConditionsSerialized());
        $rule->setStoreId($model->getStoreId());
        $this->storeManager->setCurrentStore($model->getStoreId());
        $model->setRule($rule);
        $this->updateIndex($model, $ids);
    }

    public function updateIndex(Feed $model, array $ids = []): void
    {
        $productCollection = $this->prepareCollection($model, $ids);

        $conditions = $model->getRule()->getConditions();
        $conditions->collectValidatedAttributes($productCollection);
        $this->sqlBuilder->attachConditionToCollection($productCollection, $conditions);
        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $productCollection->distinct(true);
        $currentPage = 1;
        $productCollection->setPageSize(self::BATCH_SIZE);
        $lastPage = $productCollection->getLastPageNumber();
        while ($currentPage <= $lastPage) {
            $productCollection->setCurPage($currentPage);

            $productCollection->getSelect()->reset(Select::COLUMNS);
            $select = $productCollection->getSelect()->columns(
                [
                    'entity_id' => new \Zend_Db_Expr('null'),
                    'feed_id' => new \Zend_Db_Expr((int)$model->getEntityId()),
                    'valid_product_id' => 'e.' . $productCollection->getEntity()->getIdFieldName()
                ]
            );
            //fix for magento 2.3.2 for big number of products
            $select->reset(Select::ORDER);
            $select->limitPage($currentPage, self::BATCH_SIZE);

            $query = $select->insertFromSelect($productCollection->getResource()->getTable(ValidProduct::TABLE_NAME));
            $productCollection->getConnection()->query($query);

            $currentPage++;
        }
    }

    private function prepareCollection(Feed $model, array $ids = []): ProductCollection
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addStoreFilter($model->getStoreId());

        if ($ids) {
            $productCollection->addAttributeToFilter('entity_id', ['in' => $ids]);
        }

        if ($model->getExcludeDisabled()) {
            $productCollection->addAttributeToFilter(
                'status',
                ['eq' => Status::STATUS_ENABLED]
            );
            if ($model->getExcludeSubDisabled()) {
                $this->addDisabledParentsFilter($productCollection);
            }
        }

        if ($model->getExcludeNotVisible()) {
            $productCollection->addAttributeToFilter(
                'visibility',
                ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]
            );
        }
        if ($model->getExcludeOutOfStock()) {
            $outOfStockProductIds = $this->inventoryResolver->getOutOfStockProductIds();

            if (!empty($outOfStockProductIds)) {
                $productCollection->addFieldToFilter(
                    'entity_id',
                    ['nin' => $outOfStockProductIds]
                );
            }
        }

        $model->getRule()->getConditions()->collectValidatedAttributes($productCollection);

        return $productCollection;
    }

    private function addDisabledParentsFilter(ProductCollection $productCollection): void
    {
        $subSelect = $this->getDisabledParentProductsSelect((int)$productCollection->getStoreId());

        $productCollection->getSelect()->joinLeft(
            ['rel' => $productCollection->getResource()->getTable('catalog_product_relation')],
            'rel.child_id = e.entity_id',
            []
        )->where('rel.parent_id NOT IN (?)', $subSelect);
    }

    private function getDisabledParentProductsSelect(int $storeId): Select
    {
        $disabledParentsCollection = $this->productCollectionFactory->create();
        $linkField = $disabledParentsCollection->getProductEntityMetadata()->getLinkField();

        $disabledParentsCollection->addStoreFilter($storeId);
        $disabledParentsCollection->addAttributeToFilter(
            'status',
            ['eq' => Status::STATUS_DISABLED]
        );

        return $disabledParentsCollection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(['e.' . $linkField])
            ->joinLeft(
                ['rel' => $disabledParentsCollection->getResource()->getTable('catalog_product_relation')],
                'rel.parent_id = e.' . $linkField,
                []
            )->distinct();
    }
}

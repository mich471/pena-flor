<?php
namespace WeltPixel\SmartProductTabs\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use WeltPixel\SmartProductTabs\Model\SmartProductTabs;
use WeltPixel\SmartProductTabs\Model\SmartProductTabsFactory;
use WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs\CollectionFactory as SmartProductTabsCollectionFactory;
use WeltPixel\SmartProductTabs\Model\Indexer\IndexBuilder\ProductLoader;
use Magento\Catalog\Model\Product;

class IndexBuilder
{
    /**
     * @var int
     */
    protected $batchCount;

    /**
     * @var string
     */
    protected $indexTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var SmartProductTabsCollectionFactory
     */
    protected $smartProductTabsCollectionFactory;

    /**
     * @var SmartProductTabsFactory
     */
    protected $smartProductTabsFactory;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @param ResourceConnection $resource
     * @param SmartProductTabsCollectionFactory $smartProductTabsCollectionFactory
     * @param SmartProductTabsFactory $smartProductTabsFactory
     * @param ProductLoader $productLoader
     */
    public function __construct(
        ResourceConnection $resource,
        SmartProductTabsCollectionFactory $smartProductTabsCollectionFactory,
        SmartProductTabsFactory $smartProductTabsFactory,
        ProductLoader $productLoader
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->smartProductTabsCollectionFactory = $smartProductTabsCollectionFactory;
        $this->smartProductTabsFactory = $smartProductTabsFactory;
        $this->productLoader = $productLoader;
        $this->batchCount = 1000;
        $this->indexTableName = 'weltpixel_smartproducttabs_rule_idx';
    }

    /**
     * @return int
     */
    public function getBatchCount()
    {
        return $this->batchCount;
    }

    /**
     * @return string
     */
    public function getIndexTableName()
    {
        return $this->indexTableName;
    }

    /**
     * @return array
     */
    protected function getAllSmartProductTabs()
    {
        return $this->smartProductTabsCollectionFactory->create();
    }

    /**
     * @throws \Exception
     * @return void
     */
    public function fullReindex()
    {
        $indexTableName = $this->getIndexTableName();
        $this->connection->truncateTable($this->resource->getTableName($indexTableName));
        try {
            foreach ($this->getAllSmartProductTabs() as $smartProductTab) {
                $this->executeIndexForSmartProductTab($smartProductTab);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param array $ids
     * @throws \Exception
     */
    public function reindexRule($ids)
    {
        $ids = array_unique($ids);
        $this->connection->beginTransaction();
        try {
            $this->cleanByIds($ids);
            foreach ($ids as $smartProductTabId) {
                $smartProductTab = $this->smartProductTabsFactory->create()->load($smartProductTabId);
                $this->executeIndexForSmartProductTab($smartProductTab);
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param array $ids
     * @return void
     */
    protected function cleanByIds($ids)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName($this->getIndexTableName()), 'rule_id')
                ->distinct()
                ->where('rule_id IN (?)', $ids),
            $this->resource->getTableName($this->getIndexTableName())
        );
        $this->connection->query($query);
    }

    /**
     * @param array $ids
     * @return void
     */
    protected function cleanByProductIds($ids)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName($this->getIndexTableName()), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $ids),
            $this->resource->getTableName($this->getIndexTableName())
        );
        $this->connection->query($query);
    }

    /**
     * @param \WeltPixel\SmartProductTabs\Model\SmartProductTabs $smartProductTab
     * @return bool
     */
    protected function executeIndexForSmartProductTab($smartProductTab)
    {
        $isSmartProductTabEnabled = $smartProductTab->getStatus();
        if (!$isSmartProductTabEnabled) {
            return false;
        }

        $rows = [];
        $ruleId = $smartProductTab->getId();
        $indexTableName = $this->getIndexTableName();

        $productIds = $smartProductTab->getMatchingProductIds();

        foreach ($productIds as $productIdDetails) {
            foreach ($productIdDetails as $storeId => $productId) {
                $rows[] = [
                    'rule_id' => $ruleId,
                    'product_id' => $productId,
                    'store_id' => $storeId
                ];

                if (count($rows) == $this->batchCount) {
                    $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
                    $rows = [];
                }
            }
        }

        if (!empty($rows)) {
            $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
        }

        return true;
    }

    /**
     * @param array $ids
     */
    /**
     * @param array $ids
     * @throws \Exception
     */
    public function reindexProductRule($ids)
    {
        $ids = array_unique($ids);
        $this->connection->beginTransaction();
        try {
            $this->cleanByProductIds($ids);
            $products = $this->productLoader->getProducts($ids);
            foreach ($this->getAllSmartProductTabs() as $smartProductTab) {
                foreach ($products as $product) {
                    $this->applyRule($smartProductTab, $product);
                }
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param SmartProductTabs $smartPproductTab
     * @param Product $product
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function applyRule(SmartProductTabs $smartPproductTab, $product)
    {
        $ruleId = $smartPproductTab->getId();
        $productId = $product->getId();
        $storeIds = $smartPproductTab->getAllStoreIdsAssigned();
        $indexTableName = $this->getIndexTableName();

        $this->connection->delete(
            $this->resource->getTableName($indexTableName),
            [
                $this->connection->quoteInto('rule_id = ?', $ruleId),
                $this->connection->quoteInto('product_id = ?', $productId)
            ]
        );

        $rows = [];
        try {
            foreach ($storeIds as $storeId) {
                $product->setStoreId($storeId);
                if (!$smartPproductTab->validate($product)) {
                    continue;
                }

                $rows[] = [
                    'rule_id' => $ruleId,
                    'product_id' => $productId,
                    'store_id' => $storeId
                ];

                if (count($rows) == $this->batchCount) {
                    $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
                    $rows = [];
                }
            }

            if (!empty($rows)) {
                $this->connection->insertMultiple($this->resource->getTableName($indexTableName), $rows);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }
}

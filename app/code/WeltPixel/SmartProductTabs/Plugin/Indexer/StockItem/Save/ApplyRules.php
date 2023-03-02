<?php
namespace WeltPixel\SmartProductTabs\Plugin\Indexer\StockItem\Save;

use Magento\Framework\Registry;
use WeltPixel\SmartProductTabs\Model\Indexer\Product\ProductRuleIndexer;
use Magento\Framework\Indexer\IndexerRegistry;

class ApplyRules
{
    /**
     * @var ProductRuleIndexer
     */
    protected $productRuleIndexer;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param ProductRuleIndexer $productRuleIndexer
     * @param Registry $registry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        ProductRuleIndexer $productRuleIndexer,
        Registry $registry
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->productRuleIndexer = $productRuleIndexer;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $subject
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItemResource
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function afterSave(
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $subject,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItemResource,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        $indexer = $this->indexerRegistry->get('weltpixel_smartproducttabs_product');
        $productId = $stockItem->getProductId();

        $isInStock = (bool)$stockItem->getIsInStock();
        $this->registry->register('weltpixel_smartproducttabs_product_isinstock', $isInStock);

        if (($indexer->isScheduled() == false)) {
            $this->productRuleIndexer->executeRow($productId);
        }
        $this->registry->unregister('weltpixel_smartproducttabs_product_isinstock');
        return $stockItemResource;
    }
}

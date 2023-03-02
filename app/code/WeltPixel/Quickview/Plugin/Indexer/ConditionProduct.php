<?php
namespace WeltPixel\Quickview\Plugin\Indexer;

use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\Registry;

class ConditionProduct
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Stock
     */
    protected $stockHelper;

    /**
     * @param Stock $stockHelper
     * @param Registry $registry
     */
    public function __construct(
        Stock $stockHelper,
        Registry $registry
    ) {
        $this->stockHelper = $stockHelper;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\CatalogRule\Model\Rule\Condition\Product $subject
     * @param bool $result
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate(
        \Magento\CatalogRule\Model\Rule\Condition\Product $subject,
        bool $result,
        \Magento\Framework\Model\AbstractModel $model
    ) {
        $attrCode = $subject->getAttribute();

        if ('quantity_and_stock_status' == $attrCode) {
            $isInStockRegistry = $this->registry->registry('weltpixel_quickviewmessages_product_isinstock');
            if (!is_null($isInStockRegistry) && $isInStockRegistry) {
                $result = $subject->validateAttribute($isInStockRegistry);
            } elseif ($model instanceof \Magento\Catalog\Model\Product) {
                $this->stockHelper->assignStatusToProduct($model);
                $result = $subject->validateAttribute($model->getData('is_salable'));
            }

            return (bool)$result;
        }
        return $result;
    }
}

<?php
declare(strict_types=1);

namespace SummaSolutions\FrontendCustomizations\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class SalableQuantityCheck implements ArgumentInterface
{
    protected GetProductSalableQtyInterface $getProductSalableQty;
    protected GetProductTypesBySkusInterface $getProductTypesBySkus;
    protected IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType;

    public function __construct
    (
        GetProductSalableQtyInterface $getProductSalableQty,
        GetProductTypesBySkusInterface $getProductTypesBySkus,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
    )
    {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
    }

    public function getProductSalableQty(): GetProductSalableQtyInterface
    {
        return $this->getProductSalableQty;
    }

    public function getProductTypesBySkus(): GetProductTypesBySkusInterface
    {
        return $this->getProductTypesBySkus;
    }

    public function getIsSourceItemManagementAllowedForProductType(): IsSourceItemManagementAllowedForProductTypeInterface
    {
        return $this->isSourceItemManagementAllowedForProductType;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Composite extends \Magento\CatalogImportExport\Model\Export\RowCustomizer\Composite
{
    /**
     * @var int|string
     */
    protected $storeId;

    /**
     * @var bool
     */
    protected $isParentExport = false;

    /**
     * @var array
     */
    protected $objects = [];

    /**
     * @var ExportProduct
     */
    private $exportModel;

    /**
     * @param ExportProduct $exportProduct
     */
    public function init(ExportProduct $exportProduct)
    {
        $this->exportModel = $exportProduct;

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_IMAGE_ATTRIBUTE)) {
            unset($this->customizers['imagesData']);
        }

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_GALLERY_ATTRIBUTE)) {
            unset($this->customizers['galleryData']);
        }

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_CATEGORY_ATTRIBUTE)
            && !$exportProduct->getAttributesByType(ExportProduct::PREFIX_CATEGORY_PATH_ATTRIBUTE)
            && !$exportProduct->getAttributesByType(ExportProduct::PREFIX_MAPPED_CATEGORY_ATTRIBUTE)
            && !$exportProduct->getAttributesByType(
                ExportProduct::PREFIX_MAPPED_CATEGORY_PATHS_ATTRIBUTE
            )
        ) {
            unset($this->customizers['categoryData']);
        }
        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_CUSTOM_FIELD_ATTRIBUTE)) {
            unset($this->customizers['customFieldData']);
        }

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_ADVANCED_ATTRIBUTE)) {
            unset($this->customizers['advancedData']);
        }

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_URL_ATTRIBUTE)) {
            unset($this->customizers['urlData']);
        }

        if (!$exportProduct->getAttributesByType(ExportProduct::PREFIX_PRICE_ATTRIBUTE)) {
            unset($this->customizers['priceData']);
        }

        if ($this->isParentExport || !$exportProduct->hasParentAttributes()) {
            unset($this->customizers['relationData']);
        }

        if (!$this->isParentExport
            || !isset($exportProduct->getAttributesByType(ExportProduct::PREFIX_URL_ATTRIBUTE)['configurable'])
        ) {
            unset($this->customizers['configurableProduct']);
        }
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    public function setIsParentExport(bool $isParentExport): void
    {
        $this->isParentExport = $isParentExport;
    }

    /**
     * @param string $className
     *
     * @return mixed
     */
    protected function _getObject($className)
    {
        if (!isset($this->objects[$className])) {
            $this->objects[$className] = $this->objectManager->create($className, ['export' => $this->exportModel]);
        }

        return $this->objects[$className];
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        foreach ($this->customizers as $key => $className) {
            $collection->setStoreId($this->storeId);
            $this->_getObject($className)->prepareData(clone $collection, $productIds);
        }
    }

    /**
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        $dataRow['product_id'] = $productId;

        if (!isset($dataRow['amasty_custom_data'])) {
            $dataRow['amasty_custom_data'] = [];
        }

        foreach ($this->customizers as $key => $className) {
            $dataRow = $this->_getObject($className)->addData($dataRow, $productId);
        }

        return $dataRow;
    }
}

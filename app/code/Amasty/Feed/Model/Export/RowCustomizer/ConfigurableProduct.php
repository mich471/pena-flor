<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\ConfigurableImportExport\Model\Export\RowCustomizer;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;

class ConfigurableProduct implements RowCustomizerInterface
{
    /**
     * @var array
     */
    protected $configurableData = [];

    /**
     * Prepare configurable data for export
     *
     * @param ProductCollection $collection
     * @param int[] $productIds
     * @return void
     */
    public function prepareData($collection, $productIds)
    {
        $productCollection = clone $collection;
        $productCollection->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter('type_id', ['eq' => ConfigurableProductType::TYPE_CODE]);

        while ($product = $productCollection->fetchItem()) {
            $productAttributesOptions = $product->getTypeInstance()->getConfigurableOptions($product);
            $this->configurableData[$product->getId()] = [];
            $variations = [];

            foreach ($productAttributesOptions as $attributeId => $productAttributeOption) {
                foreach ($productAttributeOption as $optValues) {
                    $variations[$optValues['sku']][$attributeId] = $optValues['value_index'];
                }
            }

            $this->configurableData[$product->getId()] = [
                RowCustomizer::CONFIGURABLE_VARIATIONS_COLUMN => $variations
            ];
        }
    }

    /**
     * @param array $dataRow
     * @param int $productId
     * @return mixed
     */
    public function addData($dataRow, $productId)
    {
        if (!empty($this->configurableData[$productId])) {
            $dataRow = array_merge($dataRow, $this->configurableData[$productId]);
        }
        return $dataRow;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function addHeaderColumns($columns)
    {
        return $columns;
    }

    /**
     * @param array $additionalRowsCount
     * @param int $productId
     * @return mixed
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }
}

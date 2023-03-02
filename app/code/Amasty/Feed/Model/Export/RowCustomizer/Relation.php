<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product;
use Amasty\Feed\Model\Export\Product as ExportProduct;
use Amasty\Feed\Model\Export\ProductFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;

class Relation implements RowCustomizerInterface
{
    /**
     * @var array
     */
    protected $parent2child;

    /**
     * @var array
     */
    protected $child2parent;

    /**
     * @var array
     */
    protected $parentData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Product
     */
    protected $export;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ProductFactory
     */
    private $productExportFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        Product $export,
        MetadataPool $metadataPool,
        ProductFactory $productExportFactory
    ) {
        $this->storeManager = $storeManager;
        $this->export = $export;
        $this->metadataPool = $metadataPool;
        $this->productExportFactory = $productExportFactory;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        $this->parentData = [];
        $parentAttributes = [
            'product' => [
                'product_id' => 'product_id'
            ],
            'url' => [
                'short' => 'short'
            ]
        ];

        if (isset($this->export->getAttributesByType(ExportProduct::PREFIX_URL_ATTRIBUTE)['configurable'])) {
            $parentAttributes['url']['configurable'] = 'configurable';
        }

        $parentAttributes = array_merge_recursive(
            $this->export->getParentAttributes(),
            $parentAttributes
        );

        if (count($parentAttributes) > 0) {
            $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
            $linkField = $productMetadata->getLinkField();
            $identifierField = $productMetadata->getIdentifierField();
            $parent2child = $child2parent = $linkFieldMap = [];
            if ($linkField != $identifierField) {
                $selectMap = $collection->getConnection()
                    ->select()
                    ->from(
                        $productMetadata->getEntityTable(),
                        [$linkField, $identifierField]
                    )->where($identifierField . ' IN(?)', $productIds)
                    ->where($linkField . ' != ' . $identifierField);

                $linkFieldMap = $collection->getConnection()->fetchPairs($selectMap);
            }

            $select = $collection->getConnection()
                ->select()
                ->from(
                    ['r' => $collection->getTable('catalog_product_relation')],
                    ['r.parent_id', 'r.child_id']
                )
                ->where('r.child_id IN(?)', $productIds);

            foreach ($collection->getConnection()->fetchAll($select) as $row) {
                $select2 = $collection->getConnection()
                    ->select()
                    ->from(
                        ['r' => $collection->getTable('catalog_product_entity')],
                        ['r.entity_id', 'r.type_id']
                    )
                    ->where('r.' . $linkField . '=(?)', $row['parent_id']);

                foreach ($collection->getConnection()->fetchAll($select2) as $row2) {
                    if ($row2['type_id'] == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                        $row['parent_id'] = $row2['entity_id'];
                    }
                }

                if (isset($row['parent_id']) && !isset($parent2child[$row['parent_id']])) {
                    $parent2child[$row['parent_id']] = [];

                    if (isset($linkFieldMap[$row['parent_id']])) {
                        $row['parent_id'] = $linkFieldMap[$row['parent_id']];
                    }
                }

                if (isset($row['child_id']) && !isset($child2parent[$row['child_id']])) {
                    $child2parent[$row['child_id']] = [];
                }

                $parent2child[$row['parent_id']][$row['child_id']] = $row['child_id'];
                $child2parent[$row['child_id']][$row['parent_id']] = $row['parent_id'];
            }

            $this->parent2child = $parent2child;
            $this->child2parent = $child2parent;

            $parentsExport = $this->productExportFactory->create(['storeId' => $collection->getStoreId()]);

            $exportData = $parentsExport
                ->setAttributes($parentAttributes)
                ->setStoreId($collection->getStoreId())
                ->setUtmParams($this->export->getUtmParams())
                ->setExcludeDisabledParents($this->export->getExcludeDisabledParents())
                ->exportParents(array_keys($this->parent2child));

            foreach ($exportData as $item) {
                if (array_key_exists('product_id', $item)) {
                    $this->parentData[$item['product_id']] = $item;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addHeaderColumns($columns)
    {
        return $columns;
    }

    /**
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        $customData = &$dataRow['amasty_custom_data'];

        if (isset($this->child2parent[$productId])) {
            foreach ($this->child2parent[$productId] as $parentId) {
                if (isset($this->parentData[$parentId])) {
                    $customData['parent_data'] = $this->parentData[$parentId];
                    break;
                }
            }
        }

        return $dataRow;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }

    /**
     * @param array $dataRow
     * @param array $parentRow
     */
    protected function _fillParentData(&$dataRow, $parentRow)
    {
        foreach ($parentRow as $key => $value) {
            if (isset($dataRow[$key])) {
                if (is_array($value)) {
                    $this->_fillParentData($dataRow[$key], $parentRow[$key]);
                } else {
                    if ($dataRow[$key] == "" && !empty($value)) {
                        $dataRow[$key] = $value;
                    }
                }
            } else {
                $dataRow[$key] = $value;
            }
        }
    }
}

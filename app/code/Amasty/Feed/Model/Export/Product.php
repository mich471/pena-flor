<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export;

use Amasty\Feed\Model\Config\Source\NumberFormat;
use Amasty\Feed\Model\Export\RowCustomizer\CompositeFactory;
use Amasty\Feed\Model\InventoryResolver;
use Amasty\Feed\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Category\StoreCategories;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttrCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory as ProductOptionCollectionFactory;
use Magento\Catalog\Model\ResourceModel\ProductFactory;
use Magento\CatalogImportExport\Model\Export\Product as ProductBase;
use Magento\CatalogImportExport\Model\Export\Product\Type\Factory as TypeFactory;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttrSetCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ImportExport\Model\Export;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\ImportExport\Model\Import as Import;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Product extends ProductBase
{
    /**
     * Attributes prefixes
     */
    public const PREFIX_CATEGORY_ATTRIBUTE = 'category';
    public const PREFIX_CATEGORY_ID_ATTRIBUTE = 'category_id';
    public const PREFIX_CATEGORY_PATH_ATTRIBUTE = 'category_path';
    public const PREFIX_MAPPED_CATEGORY_ATTRIBUTE = 'mapped_category';
    public const PREFIX_MAPPED_CATEGORY_PATHS_ATTRIBUTE = 'mapped_category_path';
    public const PREFIX_CUSTOM_FIELD_ATTRIBUTE = 'custom_field';
    public const PREFIX_PRODUCT_ATTRIBUTE = 'product';
    public const PREFIX_BASIC_ATTRIBUTE = 'basic';
    public const PREFIX_INVENTORY_ATTRIBUTE = 'inventory';
    public const PREFIX_IMAGE_ATTRIBUTE = 'image';
    public const PREFIX_GALLERY_ATTRIBUTE = 'gallery';
    public const PREFIX_PRICE_ATTRIBUTE = 'price';
    public const PREFIX_URL_ATTRIBUTE = 'url';
    public const PREFIX_OTHER_ATTRIBUTES = 'other';
    public const PREFIX_ADVANCED_ATTRIBUTE = 'advanced';

    /**
     * The shift position of the separator
     */
    public const SHIFT_OF_SEPARATOR_POSITION = 1;

    /**
     * @var array
     */
    protected $_attributes;

    /**
     * @var array
     */
    protected $_parentAttributes;

    /**
     * @var int|string
     */
    protected $_storeId;

    /**
     * @var \Amasty\Feed\Model\Export\RowCustomizer\Composite
     */
    protected $_rowCustomizer;

    /**
     * @var array
     */
    protected $_categoriesPath = [];

    /**
     * @var array
     */
    protected $_categoriesLast = [];

    /**
     * @var array
     */
    protected $_multiRowData;

    /**
     * @var array
     */
    protected $_attrCodes;

    /**
     * @var array
     */
    protected $_matchingProductIds;

    /**
     * @var int
     */
    protected $_page = 1;

    /**
     * @var int
     */
    protected $_itemsCount = 1;

    /**
     * @var bool
     */
    protected $_isLastPage = false;

    /**
     * @var array
     */
    protected $_fieldsMap = [
        Product::COL_TYPE => 'product_type',
        Product::COL_PRODUCT_WEBSITES => 'product_websites'
    ];

    /**
     * @var array
     */
    protected $_bannedAttributes = [
        'media_gallery',
        'gallery',
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'page_layout',
        'pattern'
    ];

    /**
     * @var array
     */
    protected $_utmParams;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CollectionFactory
     */
    private $collectionAmastyFactory;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var bool
     */
    private $isExcludeDisabledParents;

    /**
     * @var bool
     */
    private $currencyShow;

    /**
     * @var string
     */
    private $decimals;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var string
     */
    private $thousandSeparator;

    /**
     * @var NumberFormat
     */
    private $numberFormat;

    /**
     * @var StockRegistry
     */
    private $stockRegistry;

    /**
     * @var InventoryResolver
     */
    private $inventoryResolver;

    /**
     * @var StoreCategories
     */
    private $storeCategories;

    public function __construct(
        StockRegistry $stockRegistry,
        TimezoneInterface $localeDate,
        Config $config,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        StoreCategories $storeCategories,
        LoggerInterface $logger,
        ProductCollectionFactory $collectionFactory,
        ConfigInterface $exportConfig,
        ProductFactory $productFactory,
        AttrSetCollectionFactory $attrSetColFactory,
        CategoryCollectionFactory $categoryColFactory,
        ItemFactory $itemFactory,
        ProductOptionCollectionFactory $optionColFactory,
        ProductAttrCollectionFactory $attributeColFactory,
        TypeFactory $typeFactory,
        LinkTypeProvider $linkTypeProvider,
        CompositeFactory $rowCustomizer,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionAmastyFactory,
        NumberFormat $numberFormat,
        InventoryResolver $inventoryResolver,
        $storeId = null
    ) {
        $this->_rowCustomizer = $rowCustomizer->create();
        $this->_scopeConfig = $scopeConfig;
        $this->collectionAmastyFactory = $collectionAmastyFactory;
        $this->numberFormat = $numberFormat;
        $this->_storeId = $storeId;
        $this->stockRegistry = $stockRegistry;
        $this->inventoryResolver = $inventoryResolver;
        $this->storeCategories = $storeCategories;

        parent::__construct(
            $localeDate,
            $config,
            $resource,
            $storeManager,
            $logger,
            $collectionFactory,
            $exportConfig,
            $productFactory,
            $attrSetColFactory,
            $categoryColFactory,
            $itemFactory,
            $optionColFactory,
            $attributeColFactory,
            $typeFactory,
            $linkTypeProvider,
            $this->_rowCustomizer
        );
    }

    protected function _initStores()
    {
        $this->_storeId = $this->_storeManager->isSingleStoreMode()
            ? Store::DEFAULT_STORE_ID
            : $this->_storeId;

        $this->_storeIdToCode = [
            $this->_storeId => $this->_storeManager->getStore($this->_storeId)->getCode()
        ];

        $this->_storeManager->setCurrentStore($this->_storeId);

        return $this;
    }

    public function exportParents($ids)
    {
        $this->_initStores();

        $this->_rowCustomizer->setIsParentExport(true);

        $entityCollection = $this->_getEntityCollection(true);

        $entityCollection->setStoreId($this->_storeId);

        $entityCollection->addStoreFilter($this->_storeId);

        $this->_rowCustomizer->setStoreId($this->_storeId);

        $entityCollection->addAttributeToFilter(
            'entity_id',
            ['in' => $ids]
        );

        if ($this->getExcludeDisabledParents()) {
            $entityCollection->addAttributeToFilter(
                'status',
                ['eq' => Status::STATUS_ENABLED]
            );
        }

        parent::_prepareEntityCollection($entityCollection);

        $this->_matchingProductIds = $ids;

        $ret = $this->getExportData();

        $this->_rowCustomizer->setIsParentExport(false);

        return $ret;
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function getIsLastPage()
    {
        return $this->_isLastPage;
    }

    public function getItemsCount()
    {
        return $this->_itemsCount;
    }

    public function setPage($page)
    {
        $this->_page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatPriceCurrency()
    {
        return $this->currency;
    }

    public function getAttributeValues()
    {
        return $this->_attributeValues;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setFormatPriceCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return bool
     */
    public function getExcludeDisabledParents()
    {
        return $this->isExcludeDisabledParents;
    }

    /**
     * @param bool $isExclude
     * @return $this
     */
    public function setExcludeDisabledParents($isExclude)
    {
        $this->isExcludeDisabledParents = $isExclude;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCurrencyShow()
    {
        return $this->currencyShow;
    }

    /**
     * @param bool $currencyShow
     * @return $this
     */
    public function setCurrencyShow($currencyShow)
    {
        $this->currencyShow = $currencyShow;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatPriceDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param string $decimals
     * @return $this
     */
    public function setFormatPriceDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatPriceDecimalPoint()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function setFormatPriceDecimalPoint($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatPriceThousandsSeparator()
    {
        return $this->thousandSeparator;
    }

    /**
     * @param string $thousandSeparator
     * @return $this
     */
    public function setFormatPriceThousandsSeparator($thousandSeparator)
    {
        $this->thousandSeparator = $thousandSeparator;

        return $this;
    }

    public function getExported()
    {
        return $this->getItemsPerPage() * $this->getPage();
    }

    protected function getItemsPerPage()
    {
        return $this->_scopeConfig->getValue('amasty_feed/general/batch_size');
    }

    public function export($lastPage = false)
    {
        $this->_initStores();

        $writer = $this->getWriter();

        $entityCollection = $this->_getEntityCollection(true);

        $entityCollection->setStoreId($this->_storeId);

        $this->_rowCustomizer->setStoreId($this->_storeId);

        $this->_prepareEntityCollection($entityCollection);

        $exportData = $this->getExportData();

        if ($this->_page == 0) {
            $writer->writeHeader();
        }

        foreach ($exportData as &$dataRow) {
            $exportRow = $this->_prepareRowBeforeWrite($dataRow);
            $writer->writeDataRow($exportRow);
        }

        if ($lastPage) {
            $writer->writeFooter();
            $this->_isLastPage = true;
        }

        $this->_itemsCount = $entityCollection->getSize();

        return $writer->getContents();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getEntityCollection($resetCollection = false)
    {
        if ($resetCollection || empty($this->_entityCollection)) {
            $this->_entityCollection = $this->collectionAmastyFactory->create();
        }

        return $this->_entityCollection;
    }

    protected function addMultiRowCustomizerData(&$dataRow, &$multiRowData)
    {
        if (array_key_exists('product_id', $dataRow)) {
            $productId = $dataRow['product_id'];

            $this->updateDataWithCategoryColumns($dataRow, $multiRowData['rowCategories'], $productId);

            $dataRow = $this->rowCustomizer->addData($dataRow, $productId);
        }

        return [$dataRow];
    }

    protected function getExportData()
    {
        $exportData = [];

        $rawData = $this->collectRawData();
        $multiRowData = $this->collectMultiRowData();
        $productIds = array_keys($rawData);

        $stockItemRows = $this->inventoryResolver->getInventoryData($productIds);

        $this->rowCustomizer->init($this);

        $this->rowCustomizer->prepareData($this->_getEntityCollection(), $productIds);

        $this->setHeaderColumns($multiRowData['customOptionsData'], $stockItemRows);
        $this->_headerColumns = $this->rowCustomizer->addHeaderColumns($this->_headerColumns);

        //phpcs:disable
        foreach ($rawData as $productId => $productData) {
            foreach ($productData as $dataRow) {
                if (isset($stockItemRows[$productId])) {
                    $dataRow = array_merge($dataRow, $stockItemRows[$productId]);
                }
                $exportData = array_merge($exportData, $this->addMultiRowCustomizerData($dataRow, $multiRowData));
            }
        }
        //phpcs:enable

        return $exportData;
    }

    public function getRawExport(): array
    {
        $this->_initStores();
        $entityCollection = $this->_getEntityCollection(true);
        $entityCollection->setStoreId($this->_storeId);
        $this->_rowCustomizer->setStoreId($this->_storeId);
        $this->_prepareEntityCollection($entityCollection);

        $result = [];
        $exportData = $this->getExportData();
        foreach ($exportData as $dataRow) {
            $result[$dataRow['sku']] = $this->_prepareRowBeforeWrite($dataRow);
        }

        return $result;
    }

    public function setParentAttributes($attributes)
    {
        $this->_parentAttributes = $attributes;

        return $this;
    }

    public function getParentAttributes()
    {
        return $this->_parentAttributes;
    }

    public function hasParentAttributes()
    {
        $ret = false;

        $parentAttributes = $this->getParentAttributes();

        if (is_array($parentAttributes)) {
            foreach ($this->getParentAttributes() as $group) {
                foreach ($group as $attrs) {
                    if (isset($attrs)) {
                        $ret = true;
                        break;
                    }
                }
                if ($ret) {
                    break;
                }
            }
        }

        if (!$ret) {
            $ret = isset($this->getAttributesByType(self::PREFIX_URL_ATTRIBUTE)['configurable']);
        }

        return $ret;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function hasAttributes($key)
    {
        return isset($this->_attributes[$key]) && count($this->_attributes[$key]) > 0;
    }

    public function setAttributes($attributes)
    {
        $this->_attributes = $attributes;

        return $this;
    }

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;

        return $this;
    }

    protected function _prepareEntityCollection(\Magento\Eav\Model\Entity\Collection\AbstractCollection $collection)
    {
        $ret = parent::_prepareEntityCollection($collection);
        $ret->addFieldToFilter('entity_id', ['in' => $this->_matchingProductIds]);

        return $ret;
    }

    public function setMatchingProductIds($matchingProductIds)
    {
        $this->_matchingProductIds = $matchingProductIds;

        return $this;
    }

    protected function _prepareRowBeforeWrite(&$dataRow)
    {
        $exportRow = [];

        $dataRow = $this->_customFieldsMapping($dataRow);

        $basicTypes = [
            self::PREFIX_BASIC_ATTRIBUTE,
            self::PREFIX_PRODUCT_ATTRIBUTE,
            self::PREFIX_INVENTORY_ATTRIBUTE
        ];

        $customTypes = [
            self::PREFIX_CATEGORY_ATTRIBUTE,
            self::PREFIX_CATEGORY_PATH_ATTRIBUTE,
            self::PREFIX_MAPPED_CATEGORY_ATTRIBUTE,
            self::PREFIX_MAPPED_CATEGORY_PATHS_ATTRIBUTE,
            self::PREFIX_CUSTOM_FIELD_ATTRIBUTE,
            self::PREFIX_IMAGE_ATTRIBUTE,
            self::PREFIX_GALLERY_ATTRIBUTE,
            self::PREFIX_URL_ATTRIBUTE,
            self::PREFIX_PRICE_ATTRIBUTE,
            self::PREFIX_OTHER_ATTRIBUTES,
            self::PREFIX_ADVANCED_ATTRIBUTE
        ];

        if (is_array($this->_attributes) && !empty($this->_attributes)) {
            $this->_createExportRow($this->_attributes, $dataRow, [], $basicTypes, $customTypes, $exportRow);
        }

        if (is_array($this->_parentAttributes) && !empty($this->_parentAttributes)) {
            $parentDataRow = $dataRow['amasty_custom_data']['parent_data'] ?? [];
            $this->_createExportRow(
                $this->_parentAttributes,
                $parentDataRow,
                $dataRow,
                $basicTypes,
                $customTypes,
                $exportRow
            );
        }

        return $exportRow;
    }

    protected function _createExportRow($attributes, $dataRow, $childDataRow, $basicTypes, $customTypes, &$exportRow)
    {
        $postfix = count($childDataRow) > 0 ? '|parent' : '';

        foreach ($basicTypes as $type) {
            if (isset($attributes[$type]) && is_array($attributes[$type])) {
                foreach ($attributes[$type] as $code) {
                    $attributeValue = $this->getAttributeValue($dataRow, $code)
                        ?: $this->getAttributeValue($childDataRow, $code);

                    if ($code === 'is_in_stock') {
                        if ($this->getAttributeValue($dataRow, $code) !== false) {
                            $attributeValue = $this->getAttributeValue($dataRow, $code);
                        } elseif (isset($dataRow['sku'])) {
                            $attributeValue = $this->stockRegistry->getStockStatusBySku(
                                $dataRow['sku'],
                                $this->_storeManager->getWebsite()->getId()
                            )->getStockStatus();
                        }
                    }

                    if ($attributeValue !== false) {
                        $exportRow[$type . '|' . $code . $postfix] = $attributeValue;
                    }
                }
            }
        }

        $customData = (array)($dataRow['amasty_custom_data'] ?? []);
        $childCustomData = (array)($childDataRow['amasty_custom_data']?? []);

        foreach ($customTypes as $type) {
            if (isset($attributes[$type]) && is_array($attributes[$type])) {
                foreach ($attributes[$type] as $code) {
                    $customDataValue = $this->getAttrValueFromCustomData(
                        $customData,
                        (string)$type,
                        (string)$code
                    );
                    $childCustomDataValue = $this->getAttrValueFromCustomData(
                        $childCustomData,
                        (string)$type,
                        (string)$code
                    );
                    if ($customDataValue !== null && $customDataValue !== '') {
                        $exportRow[$type . '|' . $code . $postfix] = $customDataValue;
                    } elseif ($childCustomDataValue !== null) {
                        $exportRow[$type . '|' . $code . $postfix] = $childCustomDataValue;
                    }
                }
            }
        }
    }

    /**
     * @param array $dataRow
     * @param string $code
     *
     * @return bool|string
     */
    private function getAttributeValue($dataRow, $code)
    {
        if (isset($dataRow[$code])) {
            return $dataRow[$code];
        } elseif ($this->getValueUseAdditionalAttr($dataRow, $code)) {
            return $this->getAttrValueFromAdditionalAttr($dataRow[parent::COL_ADDITIONAL_ATTRIBUTES], $code);
        }

        return false;
    }

    /**
     * @param array $dataRow
     * @param string $code
     * @return bool
     */
    private function getValueUseAdditionalAttr($dataRow, $code)
    {
        return isset($dataRow[parent::COL_ADDITIONAL_ATTRIBUTES]) &&
            strpos($dataRow[parent::COL_ADDITIONAL_ATTRIBUTES], $code . ImportProduct::PAIR_NAME_VALUE_SEPARATOR)
            !== false;
    }

    /**
     * @param $additionalAttributesValue
     * @param $code
     *
     * @return bool|string
     */
    private function getAttrValueFromAdditionalAttr($additionalAttributesValue, $code)
    {
        $attributes = explode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $additionalAttributesValue);

        foreach ($attributes as $attribute) {
            if (strpos($attribute, $code) !== false) {
                $delimiterPosition = strpos($attribute, ImportProduct::PAIR_NAME_VALUE_SEPARATOR)
                    + self::SHIFT_OF_SEPARATOR_POSITION;

                return $delimiterPosition ? substr($attribute, $delimiterPosition) : false;
            }
        }

        return false;
    }

    /**
     * @param array $customData
     * @param string $type
     * @param string $code
     * @return mixed|string|null
     */
    private function getAttrValueFromCustomData(array $customData, string $type, string $code)
    {
        if (isset($customData[$type][$code])) {
            return is_array($customData[$type][$code])
                ? implode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $customData[$type][$code])
                : $customData[$type][$code];
        }

        return null;
    }

    protected function _getExportAttrCodes()
    {
        if (null === $this->_attrCodes) {
            if (!empty($this->_parameters[Export::FILTER_ELEMENT_SKIP])
                && is_array($this->_parameters[Export::FILTER_ELEMENT_SKIP])
            ) {
                $skipAttr = array_flip($this->_parameters[Export::FILTER_ELEMENT_SKIP]);
            } else {
                $skipAttr = [];
            }
            $attrCodes = [];

            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                if (!isset($skipAttr[$attribute->getAttributeId()])
                    || in_array($attribute->getAttributeCode(), $this->_permanentAttributes)
                ) {
                    $attrCodes[] = $attribute->getAttributeCode();
                }
            }
            $this->_attrCodes = $attrCodes;
        }

        return $this->_attrCodes;
    }

    public function getExportAttrCodesList()
    {
        $list = [];
        $exportAttrCodes = $this->_getExportAttrCodes();

        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attrCode = $attribute->getAttributeCode();

            if (in_array($attrCode, $exportAttrCodes)) {
                $list[$attrCode] = $attribute->getFrontendLabel();
            }
        }

        return $list;
    }

    public function getAttributesByType($type)
    {
        return $this->_attributes[$type] ?? [];
    }

    public function filterAttributeCollection(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $collection)
    {
        $basicAttributes = $this->getAttributesByType(self::PREFIX_BASIC_ATTRIBUTE);

        $productAttributes = $this->getAttributesByType(self::PREFIX_PRODUCT_ATTRIBUTE);

        $imageAttributes = $this->getAttributesByType(self::PREFIX_IMAGE_ATTRIBUTE);

        $attributes = array_merge($basicAttributes, $productAttributes, $imageAttributes);

        $attributes['url_key'] = 'url_key';

        foreach (parent::filterAttributeCollection($collection) as $attribute) {
            if ($this->_attributes
                && !isset($attributes[$attribute->getAttributeCode()])
            ) {
                $collection->removeItemByKey($attribute->getId());
            }
        }

        return $collection;
    }

    protected function updateDataWithCategoryColumns(&$dataRow, &$rowCategories, $productId)
    {
        if (isset($dataRow['amasty_custom_data'])) {
            $dataRow['amasty_custom_data'] = [];
        }

        $customData = &$dataRow['amasty_custom_data'];

        if (isset($rowCategories[$productId]) && !empty($rowCategories[$productId])) {
            $categories = $rowCategories[$productId];

            $storeGroupId = $this->_storeManager->getStore()->getStoreGroupId();
            $categoriesInCurrentStore = array_intersect(
                $categories,
                $this->storeCategories->getCategoryIds($storeGroupId)
            );
            $lastCategoryId = end($categoriesInCurrentStore);

            $customData[self::PREFIX_CATEGORY_ATTRIBUTE]['category'] =
                $this->_categoriesLast[$lastCategoryId] ?? '';
            $customData[self::PREFIX_CATEGORY_ID_ATTRIBUTE] = $lastCategoryId;
        }

        $ret = parent::updateDataWithCategoryColumns($dataRow, $rowCategories, $productId);

        if (isset($dataRow[self::COL_CATEGORY])) {
            $customData[self::PREFIX_CATEGORY_PATH_ATTRIBUTE]['category'] = $dataRow[self::COL_CATEGORY];
        }

        return $ret;
    }

    protected function initCategories()
    {
        $collection = $this->_categoryColFactory->create()->addNameToResult();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        foreach ($collection as $category) {
            $structure = preg_split('#/+#', $category->getPath());
            $pathSize = count($structure);

            if ($pathSize > 1) {
                $path = [];

                for ($i = 1; $i < $pathSize; $i++) {
                    if ($collection->getItemById($structure[$i])) {
                        $path[$structure[$i]] = $collection->getItemById($structure[$i])->getName();
                    } else {
                        $path[$structure[$i]] = null;
                    }
                }
                $this->_categoriesPath[$category->getId()] = $path;
                $this->_rootCategories[$category->getId()] = array_shift($path);

                if ($pathSize > 2) {
                    $this->_categories[$category->getId()] = implode('/', $path);
                }

                $this->_categoriesLast[$category->getId()] = end($this->_categoriesPath[$category->getId()]);
            }
        }

        return $this;
    }

    public function getMultiRowData()
    {
        return $this->_multiRowData;
    }

    protected function collectMultiRowData()
    {
        if (!$this->_multiRowData) {
            $data = [];
            $productIds = [];
            $rowWebsites = [];
            $rowCategories = [];

            $collection = $this->_getEntityCollection();
            $collection->setStoreId($this->_storeId);
            $collection->addCategoryIds()->addWebsiteNamesToResult();
            /** @var \Magento\Catalog\Model\Product $item */
            foreach ($collection as $item) {
                $productIds[] = $item->getId();
                $rowWebsites[$item->getId()] = array_intersect(
                    array_keys($this->_websiteIdToCode),
                    $item->getWebsites()
                );
                $rowCategories[$item->getId()] = $item->getCategoryIds();
            }
            $collection->clear();

            $allCategoriesIds = array_merge(array_keys($this->_categories), array_keys($this->_rootCategories));

            foreach ($rowCategories as &$categories) {
                $categories = array_intersect($categories, $allCategoriesIds);
            }

            $data['rowCategories'] = $rowCategories;
            $data['linksRows'] = $this->prepareLinks($productIds);
            $data['customOptionsData'] = $this->getCustomOptionsData($productIds);

            $this->_multiRowData = $data;
        }

        return $this->_multiRowData;
    }

    /**
     * Rewrite method to use actual store.
     * Original @see \Magento\ImportExport\Model\Export\Entity\AbstractEntity::getAttributeOptions
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     *
     * @return array
     */
    public function getAttributeOptions(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute)
    {
        $options = [];

        if ($attribute->usesSource()) {
            $index = in_array($attribute->getAttributeCode(), $this->_indexValueAttributes) ? 'value' : 'label';

            $attribute->setStoreId($this->_storeId ?: Store::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    foreach (is_array($option['value']) ? $option['value'] : [$option] as $innerOption) {
                        if (strlen($innerOption['value'])) {
                            // skip ' -- Please Select -- ' option
                            $options[$innerOption['value']] = (string)$innerOption[$index];
                        }
                    }
                }
            } catch (\Exception $e) {
                null;
                // ignore exceptions connected with source models
            }
        }

        return $options;
    }

    public function getMediaGallery(array $productIds)
    {
        if (empty($productIds)) {
            return [];
        }
        $productEntityJoinField = $this->getProductEntityLinkField();

        $select = $this->_connection->select()->from(
            ['mgvte' => $this->_resourceModel->getTableName('catalog_product_entity_media_gallery_value_to_entity')],
            [
                "mgvte.$productEntityJoinField",
                'mgvte.value_id'
            ]
        )->joinLeft(
            ['mg' => $this->_resourceModel->getTableName('catalog_product_entity_media_gallery')],
            '(mg.value_id = mgvte.value_id)',
            [
                'mg.attribute_id',
                'filename' => 'mg.value',
            ]
        )->joinLeft(
            ['mgv' => $this->_resourceModel->getTableName('catalog_product_entity_media_gallery_value')],
            "(mg.value_id = mgv.value_id)"
            . "and (mgvte.$productEntityJoinField = mgv.$productEntityJoinField)"
            . 'and mgv.disabled = 0',
            [
                'mgv.label',
                'mgv.position',
                'mgv.disabled',
                'mgv.store_id',
            ]
        )->joinLeft(
            ['ent' => $this->_resourceModel->getTableName('catalog_product_entity')],
            "(mgvte.$productEntityJoinField = ent.$productEntityJoinField)",
            [
                'ent.entity_id'
            ]
        )->where(
            "ent.entity_id IN (?)",
            $productIds
        )->where(
            "mgv.store_id IN (?)",
            [Store::DEFAULT_STORE_ID, $this->_storeId]
        )->order('mgv.position ASC');

        $rowMediaGallery = [];
        $stmt = $this->_connection->query($select);

        while ($mediaRow = $stmt->fetch()) {
            $rowMediaGallery[$mediaRow[$productEntityJoinField]][] = [
                '_media_attribute_id' => $mediaRow['attribute_id'],
                '_media_image' => $mediaRow['filename'],
                '_media_label' => $mediaRow['label'],
                '_media_position' => $mediaRow['position'],
                '_media_is_disabled' => $mediaRow['disabled'],
                '_media_store_id' => $mediaRow['store_id'],
            ];
        }

        return $rowMediaGallery;
    }

    public function getRootCategories()
    {
        return $this->_rootCategories;
    }

    public function getCategoriesPath()
    {
        return $this->_categoriesPath;
    }

    public function getCategoriesLast()
    {
        return $this->_categoriesLast;
    }

    public function setUtmParams($utmParams)
    {
        $this->_utmParams = $utmParams;

        return $this;
    }

    public function getUtmParams()
    {
        return $this->_utmParams;
    }
}

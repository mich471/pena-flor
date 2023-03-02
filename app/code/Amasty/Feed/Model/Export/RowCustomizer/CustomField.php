<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Api\CustomFieldsRepositoryInterface;
use Amasty\Feed\Model\Export\Product as Export;
use Amasty\Feed\Model\Export\Utils\MergedAttributeProcessor;
use Amasty\Feed\Model\Field\ResourceModel\CollectionFactory as FieldCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomField implements RowCustomizerInterface
{
    /**#@+
     * Modifier constants
     */
    public const OPERATION = 0;

    public const VALUE = 1;
    /**#@-*/

    /**
     * @var array
     */
    private $conditions = [];

    /**
     * @var Export
     */
    private $export;

    /**
     * @var \Amasty\Feed\Model\Field\ResourceModel\Collection
     */
    private $fieldCollection;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomFieldsRepositoryInterface
     */
    private $cFieldsRepository;

    /**
     * @var MergedAttributeProcessor
     */
    private $mergedAttributeProcessor;

    public function __construct(
        Export $export,
        CustomFieldsRepositoryInterface $cFieldsRepository,
        ProductRepositoryInterface $productRepository,
        FieldCollectionFactory $collectionFactory,
        MergedAttributeProcessor $mergedAttributeProcessor
    ) {
        $this->export = $export;
        $this->cFieldsRepository = $cFieldsRepository;
        $this->productRepository = $productRepository;
        $this->fieldCollection = $collectionFactory->create();
        $this->mergedAttributeProcessor = $mergedAttributeProcessor;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NoSuchEntityException
     */
    public function prepareData($collection, $productIds)
    {
        if ($this->export->hasAttributes(Export::PREFIX_CUSTOM_FIELD_ATTRIBUTE) && !$this->conditions) {
            $attributes = $this->export->getAttributesByType(Export::PREFIX_CUSTOM_FIELD_ATTRIBUTE);
            $data = $this->fieldCollection->getCustomConditions($attributes);

            if ($data) {
                foreach ($data as $record) {
                    $this->conditions[$record['code']][] = ['id' => $record['entity_id'], 'code' => $record['code']];
                }
            }

            $conformityArray = array_diff_key($attributes, $this->conditions);
            if ($conformityArray) {
                throw new NoSuchEntityException(
                    __(
                        'Error(s) occurred during feed generation, attribute code(s): "%1"',
                        implode(",", $conformityArray)
                    )
                );
            }

            $this->mergedAttributeProcessor->initialize(
                $this->conditions,
                $productIds,
                (int)$collection->getStoreId()
            );
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
        $dataRow['amasty_custom_data'][Export::PREFIX_CUSTOM_FIELD_ATTRIBUTE] = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($productId, false, $dataRow['store_id'] ?? null);

        foreach ($this->conditions as $customField) {
            foreach ($customField as $condition) {
                /** @var \Amasty\Feed\Model\Field\Condition $rule */
                $rule = $this->cFieldsRepository->getConditionModel($condition['id']);

                if ($rule->getConditions()->validate($product)) {
                    $attributeValue = null;
                    if (is_array($product->getData('tier_price'))
                        && empty($product->getData('tier_price'))
                    ) {
                        $product->setData('tier_price', '');
                    }

                    $mergedText = $rule->getFieldResult()['merged_text'] ?? null;
                    $isMergedAttribute = $mergedText !== null;
                    if (!empty($rule->getFieldResult()['attribute']) && !$isMergedAttribute) {
                        $currentAttribute = $rule->getFieldResult()['attribute'];
                        $productAttribute = $product->getData($currentAttribute);

                        if ($currentAttribute === 'quantity_and_stock_status') {
                            $attributeValue = isset($dataRow['qty']) ? (int)$dataRow['qty'] : 0;
                        } elseif ($product->getAttributeText($currentAttribute) && !is_array($productAttribute)) {
                            $attributeValue = $product->getAttributeText($currentAttribute);
                        } elseif (!is_array($productAttribute)) {
                            $attributeValue = $productAttribute;
                        }

                        $attributeValue = $this->modifyValue($attributeValue, $rule);
                    } else {
                        $attributeValue = $rule->getFieldResult()['modify'] ?? '';
                    }

                    $dataRow['amasty_custom_data'][Export::PREFIX_CUSTOM_FIELD_ATTRIBUTE][$condition['code']] =
                        !$isMergedAttribute
                            ? $attributeValue
                            : $this->mergedAttributeProcessor->execute($product, $mergedText);
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
     * @param array|string|null $value
     * @param \Amasty\Feed\Model\Field\Condition $rule
     *
     * @return float|int|string
     */
    private function modifyValue($value, $rule)
    {
        if ($value) {
            $modifier = isset($rule->getFieldResult()['modify']) ? $rule->getFieldResult()['modify'] : '';

            if ($modifier && is_numeric($value)) {
                $value = $this->modifyNumeric($modifier, $value);
            }
        }

        return $value;
    }

    /**
     * Return modified value or modifier itself if modifier does not match the pattern.
     * Modifier patterns: (+ or -)number(%).
     *
     * @param string $modifier
     * @param float|int $value
     *
     * @return float|int
     */
    private function modifyNumeric($modifier, $value)
    {
        $modifierArray =
            preg_split('/([\d]+([.,][\d]+)?)/', $modifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $modifierValue = isset($modifierArray[self::VALUE]) ? str_replace(',', '.', $modifierArray[self::VALUE]) : 0;

        if ($modifierValue && end($modifierArray) === '%') {
            $modifierValue = $value * $modifierValue / 100;
        }

        switch ($modifierArray[self::OPERATION]) {
            case '-':
                $value -= $modifierValue;
                break;
            case '+':
                $value += $modifierValue;
                break;
        }

        return $value;
    }
}

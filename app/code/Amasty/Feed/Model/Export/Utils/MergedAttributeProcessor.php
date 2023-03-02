<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\Utils;

use Amasty\Feed\Api\CustomFieldsRepositoryInterface;
use Amasty\Feed\Model\Export\ProductFactory as ExportProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

class MergedAttributeProcessor
{
    private const ATTR_MARKER = '{}';
    private const PARENT_MARKER = 'parent';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomFieldsRepositoryInterface
     */
    private $customFieldsRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var ExportProductFactory
     */
    private $exportProductFactory;

    /**
     * @var array
     */
    private $mergedAttrReplacement = [];

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CustomFieldsRepositoryInterface $customFieldsRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ExportProductFactory $exportProductFactory
    ) {
        $this->productRepository = $productRepository;
        $this->customFieldsRepository = $customFieldsRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->exportProductFactory = $exportProductFactory;
    }

    public function initialize(array $conditions, array $productIds, int $storeId)
    {
        $this->mergedAttrReplacement = $this->getMergedAttributesReplacement(
            $this->getRules($conditions),
            $productIds,
            $storeId
        );
    }

    public function execute(Product $product, string $mergedText): string
    {
        $replace = $this->mergedAttrReplacement[$product->getSku()] ?? null;
        if ($replace) {
            return strtr($mergedText, $replace);
        }

        return $mergedText;
    }

    private function getRules(array $conditions): array
    {
        $rules = [];
        foreach ($conditions as $customField) {
            foreach ($customField as $condition) {
                $rule = $this->customFieldsRepository->getConditionModel($condition['id']);
                $mergedText = $rule->getFieldResult()['merged_text'] ?? null;
                if ($mergedText !== null) {
                    $rules[$rule->getId()] = $rule;
                }
            }
        }

        return $rules;
    }

    private function getProductList(array $productIds, int $storeId): array
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter('entity_id', $productIds, 'in')
            ->addFilter('store_id', $storeId)
            ->create();

        return $this->productRepository->getList($searchCriteria)->getItems();
    }

    private function getValidProductIds(array $rules, array $productIds, int $storeId): array
    {
        $validIds = [];
        if ($rules) {
            foreach ($this->getProductList($productIds, $storeId) as $product) {
                foreach ($rules as $rule) {
                    if ($rule->getConditions()->validate($product)) {
                        $validIds[] = $product->getId();
                        continue 2;
                    }
                }
            }
        }

        return $validIds;
    }

    private function parseMergedText(array $rules): array
    {
        $parsedData = [];
        foreach ($rules as $rule) {
            $mergedText = $rule->getFieldResult()['merged_text'] ?? '';
            preg_match_all('/{(.*?)}/', $mergedText, $matches);
            foreach ($matches[0] as $item) {
                $attribute = trim($item, self::ATTR_MARKER);
                $attributeData = explode('|', $attribute, 3);
                $parsedData[$item] = [
                    'type' => $attributeData[0] ?? '',
                    'code' => $attributeData[1] ?? '',
                    'parent' => ($attributeData[2] ?? '') == self::PARENT_MARKER,
                    'attribute' => $attribute
                ];
            }
        }

        return $parsedData;
    }

    private function getAttributes(array $parsedData, bool $isParent = false): array
    {
        $attributes = [];
        foreach ($parsedData as $attrData) {
            if ($attrData['parent'] == $isParent) {
                $attributes[$attrData['type']][$attrData['code']] = $attrData['code'];
            }
        }

        return $attributes;
    }

    private function getMergedAttributesReplacement(array $rules, array $productIds, int $storeId): array
    {
        $replace = [];
        $productIds = $this->getValidProductIds($rules, $productIds, $storeId);
        if ($productIds) {
            $export = $this->exportProductFactory->create(['storeId' => $storeId]);
            $parsedData = $this->parseMergedText($rules);
            $exportData = $export->setAttributes($this->getAttributes($parsedData))
                ->setParentAttributes($this->getAttributes($parsedData, true))
                ->setMatchingProductIds($productIds)
                ->getRawExport();

            foreach ($exportData as $sku => $item) {
                foreach ($parsedData as $substr => $attrData) {
                    $replace[$sku][$substr] = $item[$attrData['attribute']] ?? '';
                }
            }

            return $replace;
        }

        return $replace;
    }
}

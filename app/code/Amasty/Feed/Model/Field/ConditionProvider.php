<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field;

use Amasty\Feed\Api\CustomFieldsRepositoryInterface;
use Amasty\Feed\Block\Adminhtml\Field\Edit\Conditions as ConditionsBlock;
use Amasty\Feed\Model\Config\Source\CustomFieldType;
use Amasty\Feed\Model\Field\Utils\FieldNameResolver;
use Magento\Framework\App\Request\DataPersistorInterface;

class ConditionProvider
{
    /**
     * @var CustomFieldsRepositoryInterface
     */
    private $cFieldsRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FieldNameResolver
     */
    private $fieldNameResolver;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        CustomFieldsRepositoryInterface $cFieldsRepository,
        DataPersistorInterface $dataPersistor,
        FieldNameResolver $fieldNameResolver
    ) {
        $this->cFieldsRepository = $cFieldsRepository;
        $this->dataPersistor = $dataPersistor;
        $this->fieldNameResolver = $fieldNameResolver;
    }

    /**
     * Get field conditions and output value information by type
     *
     * @param int $fieldId
     * @param string $type
     * @return Condition
     */
    public function getCondition(
        int $fieldId,
        string $type = FieldNameResolver::TYPE_BY_CONDITIONS
    ): Condition {
        $this->setType($type);
        $restoredCondition = $this->restoreUnsavedData();
        if ($restoredCondition) {
            return $restoredCondition;
        }

        return $this->prepareResultData($this->resolveCondition($fieldId));
    }

    private function setType(string $type): void
    {
        $this->type = $type;
    }

    private function prepareResultData(Condition $condition): Condition
    {
        $fieldResult = $condition->getFieldResult();
        $mergedText = $fieldResult['merged_text'] ?? '';

        if ($mergedText !== '') {
            $fieldResult['entity_type'] = CustomFieldType::MERGED_ATTRIBUTES;
        } elseif (empty($fieldResult['attribute'])) {
            $fieldResult['entity_type'] = CustomFieldType::CUSTOM_TEXT;
            $fieldResult['custom_text'] = !empty($fieldResult['modify']) ? $fieldResult['modify'] : '';
            $fieldResult['modify'] = '';
        } else {
            $fieldResult['entity_type'] = CustomFieldType::ATTRIBUTE;
        }

        $dataPrefix = $this->fieldNameResolver->getResultFieldName($this->type);
        foreach ($fieldResult as $key => $value) {
            $key = $dataPrefix . '[' . $key . ']';
            $condition->setData($key, $value);
        }

        return $condition;
    }

    private function resolveCondition(int $fieldId): Condition
    {
        $actualIds = $this->cFieldsRepository->getConditionsIds($fieldId);
        $resultId = $actualIds[0] ?? null;
        if ($this->type == FieldNameResolver::TYPE_DEFAULT) {
            $resultId = $actualIds[1] ?? null;
        }

        return $this->cFieldsRepository->getConditionModel($resultId);
    }

    /**
     * Try to get unsaved data if error was occurred.
     * Return Condition if the data was received.
     *
     * @return Condition|null
     */
    private function restoreUnsavedData(): ?Condition
    {
        $condition = $this->cFieldsRepository->getConditionModel();
        $tempData = $this->dataPersistor->get(ConditionsBlock::FORM_NAMESPACE);

        if ($tempData) {
            $dataKey = $this->fieldNameResolver->getRuleFieldName($this->type);
            $condition->loadPost($tempData[$dataKey] ?? []);
            $condition->getConditions();

            return $this->prepareResultData($condition);
        }

        return null;
    }
}

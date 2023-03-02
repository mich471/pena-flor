<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Rule\Condition\Sql;

use Amasty\Feed\Model\Rule\Condition\Product\Inventory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;

class Builder extends \Magento\Rule\Model\Condition\Sql\Builder
{
    /**
     * @var string[]
     */
    protected $additionalConditionOperatorMap = [
        '<=>' => ':field IS NULL'
    ];

    /**
     * @var string[]
     */
    private $stringConditionOperatorMap = [
        '{}' => ':field LIKE ?',
        '!{}' => ':field NOT LIKE ?',
    ];

    /**
     * @var bool
     */
    private $isNotDefaultStoreUsed = false;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    public function __construct(
        ExpressionFactory $expressionFactory,
        EavConfig $eavConfig,
        AttributeRepositoryInterface $attributeRepository = null
    ) {
        parent::__construct($expressionFactory, $attributeRepository);
        $this->eavConfig = $eavConfig;

        $this->_conditionOperatorMap = array_merge(
            $this->additionalConditionOperatorMap,
            $this->_conditionOperatorMap
        );
    }

    protected function _getMappedSqlCondition(
        AbstractCondition $condition,
        string $value = '',
        bool $isDefaultStoreUsed = true
    ): string {
        $argument = $condition->getMappedSqlField();

        // If rule hasn't valid argument - prevent incorrect rule behavior.
        if (empty($argument)) {
            return $this->_expressionFactory->create(['expression' => '1 = -1']);
        } elseif (preg_match('/[^a-z\d\-_\.\`]/i', $argument) > 0) {
            throw new LocalizedException(__('Invalid field'));
        }

        $conditionOperator = $condition->getOperatorForValidate();
        if (!isset($this->_conditionOperatorMap[$conditionOperator])) {
            throw new LocalizedException(__('Unknown condition operator'));
        }

        switch (true) {
            case $condition instanceof Inventory:
                $expression = $this->getInventoryExpression($condition, $value, $isDefaultStoreUsed);
                break;
            default:
                $expression = $this->getProductExpression($condition, $value, $isDefaultStoreUsed);
        }

        return (string)$this->_expressionFactory->create(
            ['expression' => $expression]
        );
    }

    private function getProductExpression(
        AbstractCondition $condition,
        string $value = ''
    ): string {
        $argument = $condition->getMappedSqlField();
        $conditionOperator = $condition->getOperatorForValidate();
        $defaultValue = 0;

        // Check if attribute has a table with default value and add it to the query
        if ($this->isNotDefaultStoreUsed && $this->canAttributeHaveDefaultValue($condition->getAttribute())) {
            $defaultField = AbstractCollection::ATTRIBUTE_TABLE_ALIAS_PREFIX
                . $condition->getAttribute()
                . '_default.value';
            $defaultValue = $this->_connection->quoteIdentifier($defaultField);
        }

        //operator 'contains {}' is mapped to 'IN()' query that cannot work with substrings
        // adding mapping to 'LIKE %%'
        if ($condition->getInputType() === 'string'
            && in_array($conditionOperator, array_keys($this->stringConditionOperatorMap), true)
        ) {
            $sql = str_replace(
                ':field',
                $this->_connection->getIfNullSql($this->_connection->quoteIdentifier($argument), $defaultValue),
                $this->stringConditionOperatorMap[$conditionOperator]
            );
            $bindValue = $condition->getBindArgumentValue();
            $expression = $value . $this->_connection->quoteInto($sql, "%$bindValue%");
        } elseif ($condition->getOperator() === '<=>') {
            $sql = str_replace(
                ':field',
                $this->_connection->quoteIdentifier($argument),
                $this->_conditionOperatorMap[$conditionOperator]
            );
            $bindValue = $condition->getBindArgumentValue();
            $expression = $value . $this->_connection->quoteInto($sql, $bindValue);
        } else {
            $sql = str_replace(
                ':field',
                $this->_connection->getIfNullSql($this->_connection->quoteIdentifier($argument), $defaultValue),
                $this->_conditionOperatorMap[$conditionOperator]
            );
            $bindValue = $condition->getBindArgumentValue();
            $expression = $value . $this->_connection->quoteInto($sql, $bindValue);
        }

        // values for multiselect attributes can be saved in comma-separated format
        // below is a solution for matching such conditions with selected values
        if (is_array($bindValue) && in_array($conditionOperator, ['()', '{}'], true)) {
            foreach ($bindValue as $item) {
                $expression .= $this->_connection->quoteInto(
                    " OR (FIND_IN_SET (?, {$this->_connection->quoteIdentifier($argument)}) > 0)",
                    $item
                );
            }
        }

        return $expression;
    }

    private function getInventoryExpression(
        AbstractCondition $condition,
        string $value = ''
    ): string {
        $conditionOperator = $condition->getOperatorForValidate();
        $argument = $condition->getMappedSqlField();

        $sql = str_replace(
            ':field',
            $this->_connection->quoteIdentifier('i.'.$condition->getAttribute()),
            $this->_conditionOperatorMap[$conditionOperator]
        );
        $bindValue = $condition->getBindArgumentValue();
        $expression = $value . $this->_connection->quoteInto($sql, $bindValue);

        if (is_array($bindValue) && in_array($conditionOperator, ['()', '{}'], true)) {
            foreach ($bindValue as $item) {
                $expression .= $this->_connection->quoteInto(
                    " OR (FIND_IN_SET (?, {$this->_connection->quoteIdentifier($argument)}) > 0)",
                    $item
                );
            }
        }

        return $expression;
    }

    private function canAttributeHaveDefaultValue(string $attributeCode): bool
    {
        if ($attributeCode) {
            try {
                $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);

                return $attribute->getAttributeId() && !$attribute->isScopeGlobal();
            } catch (LocalizedException $e) {
                // It's not exceptional case as we want to check if we have such attribute or not
                null;
            }
        }

        return false;
    }

    public function attachConditionToCollection(
        AbstractCollection $collection,
        Combine $combine
    ): void {
        $this->isNotDefaultStoreUsed = (int)$collection->getStoreId() !== (int)$collection->getDefaultStoreId();
        parent::attachConditionToCollection($collection, $combine);
    }
}

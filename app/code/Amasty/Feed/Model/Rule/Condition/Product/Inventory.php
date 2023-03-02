<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Rule\Condition\Product;

use Amasty\Feed\Model\InventoryResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class Inventory extends AbstractCondition
{
    public const QTY_ATR = 'qty';
    public const STOCK_ITEM_TABLE = 'cataloginventory_stock_item';

    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @var array
     */
    private $filterAttributes = [];

    /**
     * @var InventoryResolver
     */
    private $inventoryResolver;

    public function __construct(
        Context $context,
        InventoryResolver $inventoryResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->inventoryResolver = $inventoryResolver;
    }

    public function getNewChildSelectOptions(): array
    {
        $attributes = [];
        foreach ($this->loadAttributeOptions()->getAttributeOption() as $attrCode => $attrLabel) {
            $attributes[] = [
                'value' => Inventory::class . '|' . $attrCode,
                'label' => $attrLabel,
            ];
        }

        return $attributes;
    }

    public function loadAttributeOptions(): AbstractCondition
    {
        $this->setAttributeOption([self::QTY_ATR => __('Product Total Quantity')]);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    public function collectValidatedAttributes($productCollection): self
    {
        $linkField = $productCollection->getEntity()->getLinkField();

        if (empty($this->filterAttributes[$this->getAttribute()])) {
            $productCollection->getSelect()->joinLeft(
                ['i' => $productCollection->getResource()->getTable(self::STOCK_ITEM_TABLE)],
                "e.$linkField = i.product_id",
                '*'
            );
            $this->filterAttributes[$this->getAttribute()] = true;
        }

        return $this;
    }

    public function validate(AbstractModel $model): bool
    {
        $attrCode = $this->getAttribute();
        if ($attrCode == self::QTY_ATR) {
            try {
                $stockData = $this->inventoryResolver->getInventoryData([$model->getEntityId()]);
                if (!empty($stockData)) {
                    $item = $stockData[array_key_first($stockData)];

                    return $this->validateAttribute((int)($item['qty'] ?? 0));
                }
            } catch (NoSuchEntityException $e) {
                null;
            }
        }

        return parent::validate($model);
    }

    public function getMappedSqlField(): string
    {
        if ($this->getAttribute() === self::QTY_ATR) {
            return 'i.' . $this->getAttribute();
        }

        return parent::getMappedSqlField();
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Rule\Condition;

use Amasty\Feed\Model\Rule\Condition\Product\InventoryFactory;
use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\CatalogRule\Model\Rule\Condition\Combine
{
    /**
     * @var Product\InventoryFactory
     */
    private $inventoryFactory;

    public function __construct(
        Context $context,
        ProductFactory $conditionFactory,
        InventoryFactory $inventoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $conditionFactory, $data);

        $this->setType(Combine::class);
        $this->inventoryFactory = $inventoryFactory;
    }

    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_productFactory->create()->loadAttributeOptions()->getAttributeOption();

        $productAttributes['type_id'] = __('Type');

        $attributesOptions = [];
        foreach ($productAttributes as $code => $label) {
            $attributesOptions[] = [
                'value' => Product::class . '|' . $code,
                'label' => $label,
            ];
        }
        $inventoryConditions = $this->inventoryFactory->create();

        return array_merge_recursive(
            [['value' => '', 'label' => __('Please choose a condition to add.')]],
            [
                [
                    'value' => Combine::class,
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $attributesOptions],
                ['label' => __('Inventory Conditions'), 'value' => $inventoryConditions->getNewChildSelectOptions()]
            ]
        );
    }
}

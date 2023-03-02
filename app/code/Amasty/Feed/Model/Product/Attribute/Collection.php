<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Product\Attribute;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
{
    /**
     * Return product attributes which cat be used for promo rule conditions as sorted array
     *
     * @return array
     */
    public function getAttributesArray()
    {
        $this->getSelect()->reset(Select::COLUMNS)->columns(
            [
                \Magento\Eav\Api\Data\AttributeInterface::ATTRIBUTE_CODE,
                \Magento\Eav\Api\Data\AttributeInterface::FRONTEND_LABEL
            ]
        )->where('is_used_for_promo_rules = ?', 1);

        return $this->setOrder(
            \Magento\Eav\Api\Data\AttributeInterface::FRONTEND_LABEL,
            \Magento\Framework\Data\Collection::SORT_ORDER_ASC
        )->getData();
    }
}

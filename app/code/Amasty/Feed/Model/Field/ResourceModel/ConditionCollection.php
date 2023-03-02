<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ConditionCollection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            \Amasty\Feed\Model\Field\Condition::class,
            \Amasty\Feed\Model\Field\ResourceModel\Condition::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}

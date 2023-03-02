<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            \Amasty\Feed\Model\Field\Field::class,
            \Amasty\Feed\Model\Field\ResourceModel\Field::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @return $this
     */
    public function getSortedCollection()
    {
        $this->addOrder('name');

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function getCustomConditions($fields)
    {
        $where = $this->_translateCondition('code', ['in' => $fields]);

        $this->getSelect()->reset(Select::COLUMNS)->joinInner(
            ['cond' => $this->getTable(Condition::TABLE_NAME)],
            'cond.feed_field_id = main_table.feed_field_id',
            ['cond.entity_id', 'main_table.code']
        )->where($where);

        return $this->getData();
    }
}

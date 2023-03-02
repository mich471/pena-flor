<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Condition extends AbstractDb
{
    public const TABLE_NAME = 'amasty_feed_field_conditions';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }

    /**
     * @param int $fieldId
     */
    public function deleteAllByFieldId($fieldId)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $query = $connection->deleteFromSelect(
            $connection->select()->from($this->getMainTable(), 'feed_field_id')->where(
                'feed_field_id IN (?)',
                $fieldId
            ),
            $this->getMainTable()
        );

        $connection->query($query);
    }

    /**
     * @param int $fieldId
     *
     * @return array
     */
    public function getIdsByField($fieldId)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $query = $connection->select()->from($this->getMainTable())->columns('entity_id')->where(
            'feed_field_id IN (?)',
            $fieldId
        );

        return $connection->fetchCol($query);
    }
}

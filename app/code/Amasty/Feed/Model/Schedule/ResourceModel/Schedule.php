<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Schedule\ResourceModel;

use Amasty\Feed\Api\Data\ScheduleInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Schedule extends AbstractDb
{
    public const TABLE_NAME = 'amasty_feed_schedule';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, ScheduleInterface::ID);
    }

    /**
     * @param int $feedId
     */
    public function deleteByFeedId($feedId)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();

        $query = $connection->deleteFromSelect(
            $connection->select()->from($this->getMainTable(), ScheduleInterface::FEED_ID)->where(
                ScheduleInterface::FEED_ID . ' IN (?)',
                $feedId
            ),
            $this->getMainTable()
        );

        $connection->query($query);
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\ResourceModel;

use Amasty\Feed\Api\Data\FeedInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Feed extends AbstractDb
{
    public const TABLE_NAME = 'amasty_feed_entity';
    public const ID = 'entity_id';

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID);
    }

    /**
     * Return array of main info about Feed profiles.
     *
     * @return array
     */
    public function getProfilesMainData()
    {
        $select = $this->getConnection()->select();
        $select->from(
            $this->getMainTable(),
            [
                FeedInterface::ENTITY_ID,
                FeedInterface::NAME,
                FeedInterface::FILENAME => "CONCAT(filename, '.', feed_type)",
                FeedInterface::GENERATED_AT
            ]
        )->where(FeedInterface::IS_TEMPLATE . ' = 0');

        return $this->getConnection()->fetchAll($select);
    }
}

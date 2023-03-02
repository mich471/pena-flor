<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Field extends AbstractDb
{
    public const TABLE_NAME = 'amasty_feed_field';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'feed_field_id');
    }
}

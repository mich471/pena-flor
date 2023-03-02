<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category\ResourceModel;

use Amasty\Feed\Model\Category\Category as ModelCategory;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Category extends AbstractDb
{
    public const TABLE_NAME = 'amasty_feed_category';

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'feed_category_id');
    }
}

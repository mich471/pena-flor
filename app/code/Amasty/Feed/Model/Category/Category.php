<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category;

use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel
{
    public const FEED_CATEGORY_ID = 'feed_category_id';

    protected function _construct()
    {
        $this->_init(\Amasty\Feed\Model\Category\ResourceModel\Category::class);
        $this->setIdFieldName(self::FEED_CATEGORY_ID);
    }
}

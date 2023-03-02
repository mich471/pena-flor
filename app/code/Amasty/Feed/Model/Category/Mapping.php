<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category;

use Magento\Framework\Model\AbstractModel;

class Mapping extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Amasty\Feed\Model\Category\ResourceModel\Mapping::class);
        $this->setIdFieldName('entity_id');
    }
}

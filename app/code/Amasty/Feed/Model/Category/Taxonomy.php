<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category;

use Amasty\Feed\Model\Category\ResourceModel\Taxonomy as ResourceTaxonomy;

class Taxonomy extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceTaxonomy::class);
    }
}

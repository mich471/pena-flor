<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Feed\Model\Category\Category::class,
            \Amasty\Feed\Model\Category\ResourceModel\Category::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Add google setup filter
     *
     * @return $this
     */
    public function addGoogleSetupFilter()
    {
        $this->addFieldToFilter(
            'code',
            ['like' => 'google_category_%']
        );

        return $this;
    }
}

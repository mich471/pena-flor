<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BalloonGroup\General\Model\ResourceModel\Fulltext;


class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    public function setCustomSizeForFilter($customProducts)
    {
        $this->_totalRecords = $customProducts; 
    } 
}
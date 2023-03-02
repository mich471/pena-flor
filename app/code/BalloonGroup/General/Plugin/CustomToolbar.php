<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace BalloonGroup\General\Plugin;

class CustomToolbar 
{
       /**
     * Total number of products in current category.
     *
     * @return int
     */
    public function afterGetTotalNum(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result)
    {
        $collection = $subject->getCollection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if($subject->getLimit() == $collection->count()){
            return $collection->getSize();
        }
        return $collection->count();
    }
}

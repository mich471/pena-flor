<?php
/*
 *   @author Gilberto Aguirre <gilberto.aguirre@balloon-group.com> || Ballon Group Team
 *   @copyright Copyright (c) 2022 Ballon Group (https://www.balloon-group.com/)
 *   @package BalloonGroup_OutOfStock
*/
namespace BalloonGroup\OutOfStock\Plugin\Catalog\Model;

use Magento\Catalog\Model\Layer as LayerModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Layer
{
    public function beforePrepareProductCollection(LayerModel $layer, Collection $collection)
    {
        $collection->getSelect()->order('is_salable DESC');
        $orderBys = $collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        array_unshift($orderBys, array_pop($orderBys));
        $collection->getSelect()->setPart(\Zend_Db_Select::ORDER, $orderBys);

        return [$collection];
    }
}
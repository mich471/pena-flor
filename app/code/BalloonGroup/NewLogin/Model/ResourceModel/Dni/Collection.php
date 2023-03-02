<?php
namespace BalloonGroup\NewLogin\Model\ResourceModel\Dni;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'BalloonGroup\NewLogin\Model\Dni',
            'BalloonGroup\NewLogin\Model\ResourceModel\Dni'
        );
    }
}

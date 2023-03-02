<?php
namespace BalloonGroup\NewLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Dni extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('balloon_newlogin_authorized_dnis', 'authorized_dni_id');
    }
}

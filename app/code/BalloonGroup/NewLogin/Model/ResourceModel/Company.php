<?php
namespace BalloonGroup\NewLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Company extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('balloon_newlogin_companies', 'company_id');
    }
}

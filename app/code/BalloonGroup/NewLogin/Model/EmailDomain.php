<?php
namespace BalloonGroup\NewLogin\Model;

use Magento\Framework\Model\AbstractModel;

class EmailDomain extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain');
    }
}

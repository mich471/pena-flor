<?php
namespace BalloonGroup\NewLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class EmailDomain extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('balloon_newlogin_email_domains', 'email_domain_id');
    }
}

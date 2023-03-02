<?php
namespace BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'BalloonGroup\NewLogin\Model\EmailDomain',
            'BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain'
        );
    }
}

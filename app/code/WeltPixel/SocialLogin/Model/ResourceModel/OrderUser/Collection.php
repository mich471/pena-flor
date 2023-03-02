<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\ResourceModel\OrderUser;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package WeltPixel\SocialLogin\Model\ResourceModel\OrderUser
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('WeltPixel\SocialLogin\Model\OrderUser', 'WeltPixel\SocialLogin\Model\ResourceModel\OrderUser');
    }
}
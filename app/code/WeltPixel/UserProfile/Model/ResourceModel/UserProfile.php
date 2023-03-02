<?php

namespace WeltPixel\UserProfile\Model\ResourceModel;

/**
 * Class UserProfile
 * @package WeltPixel\UserProfile\Model\ResourceModel
 */
class UserProfile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_user_profile', 'profile_id');
    }

    /**
     * Load userProfile by customer id
     *
     * @param \WeltPixel\UserProfile\Model\UserProfile $userProfile
     * @param integer $customerId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomerId(\WeltPixel\UserProfile\Model\UserProfile $userProfile, $customerId)
    {
        $connection = $this->getConnection();
        $bind = ['customer_id' => $customerId];
        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'customer_id = :customer_id'
        );

        $userProfileId = $connection->fetchOne($select, $bind);
        if ($userProfileId) {
            $this->load($userProfile, $userProfileId);
        } else {
            $userProfile->setData([]);
        }

        return $this;
    }

    /**
     * @param \WeltPixel\UserProfile\Model\UserProfile $userProfile
     * @param string $username
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByUsername(\WeltPixel\UserProfile\Model\UserProfile $userProfile, $username)
    {
        $connection = $this->getConnection();
        $bind = ['username' => $username];
        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'username = :username'
        );

        $userProfileId = $connection->fetchOne($select, $bind);
        if ($userProfileId) {
            $this->load($userProfile, $userProfileId);
        } else {
            $userProfile->setData([]);
        }

        return $this;
    }
}

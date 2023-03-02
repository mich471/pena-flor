<?php

namespace WeltPixel\UserProfile\Model;

/**
 * Class UserProfile
 *
 * @method int getProfileId()
 * @method \WeltPixel\UserProfile\Model\Profile setProfileId($value)
 * @method int getCustomerId()
 * @method \WeltPixel\UserProfile\Model\Profile setCustomerId($value)
 * @method string getUsername()
 * @method \WeltPixel\UserProfile\Model\Profile setUsername($value)
 * @method string getAvatar()
 * @method \WeltPixel\UserProfile\Model\Profile setAvatar($value)
 * @method string getCoverImage()
 * @method \WeltPixel\UserProfile\Model\Profile setCoverImage($value)
 * @method string getFirstName()
 * @method \WeltPixel\UserProfile\Model\Profile setFirstName($value)
 * @method string getLastName()
 * @method \WeltPixel\UserProfile\Model\Profile setLastName($value)
 * @method string getLocation()
 * @method \WeltPixel\UserProfile\Model\Profile setLocation($value)
 * @method string getDob()
 * @method \WeltPixel\UserProfile\Model\Profile setDob($value)
 * @method string getBio()
 * @method \WeltPixel\UserProfile\Model\Profile setBio($value)
 * @method string getGender()
 * @method \WeltPixel\UserProfile\Model\Profile setGender($value)
 *
 *
 * @package WeltPixel\UserProfile\Model
 */
class UserProfile extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{

    const MEDIA_AVATAR_PATH = 'weltpixel/userprofile/avatar';
    const MEDIA_COVER_PATH = 'weltpixel/userprofile/cover';
    const MEDIA_IMAGES_PATH = 'weltpixel/userprofile/images';
    const MEDIA_IMAGES_WIDTH = '800';
    const MEDIA_AVATAR_WIDTH = '200';
    const MEDIA_AVATAR_HEIGHT = '200';
    const USERNAME_MAX_LENGTH = 20;
    const CACHE_TAG = 'weltpixel_userprofile';

    /**
     * @var string
     */
    protected $_cacheTag = 'weltpixel_userprofile';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'weltpixel_userprofile';


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\UserProfile\Model\ResourceModel\UserProfile');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getId()) {
            $identities = [self::CACHE_TAG . '_' . $this->getId()];
        }
        return $identities;
    }

    /**
     * @param $customerId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomerId($customerId)
    {
        $this->_getResource()->loadByCustomerId($this, $customerId);
        return $this;
    }

    /**
     * @param $username
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByUsername($username)
    {
        $this->_getResource()->loadByUsername($this, $username);
        return $this;
    }
}

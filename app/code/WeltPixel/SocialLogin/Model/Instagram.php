<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Instagram
 * @package WeltPixel\SocialLogin\Model
 */
class Instagram extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'instagram';

    /**
     * @var array
     */
    protected $_toUnset = [
        'username',
        'profile_picture',
        'bio',
        'full_name',
        'website',
        'is_business'
    ];
    /**
     * @var string
     */
    protected $_url = 'https://api.instagram.com/oauth/authorize';

    protected $_apiTokenUrl = 'https://api.instagram.com/oauth/access_token';
    /**
     * @var array
     */
    protected $_fields = [
        'user_id' => 'id',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'email',
        'gender' => 'gender'
    ];

    public function _construct()
    {
        parent::_construct();

    }

    /**
     * @param $response
     * @return bool
     */
    public function fetchUserData($response)
    {
        if (empty($response)) {
            return false;
        }

        $data = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'grant_type' =>  'authorization_code',
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'POST')) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
        }

        $userData = $this->_setUserName($data['user']);
        $userData = $this->_setUserEmail($userData);
        $userData = $this->_unsetUserData($userData);

        if (!$this->_userData = $this->_setSocialUserData($userData)) {
            return false;
        }

        if ($this->isUserProfileCreationEnabled()) {
            $this->prepareUserProfileData($data['user']);
        }

        return true;
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _setSocialUserData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        return parent::_setSocialUserData($data);
    }

    /**
     * @param $userData
     */
    protected function _setUserName($userData) {
        if(isset($userData['full_name']) && !empty($userData['full_name'])) {
            $nameArr = explode(' ', $userData['full_name']);
            $userData['firstname'] = isset($nameArr[0]) ? $nameArr[0] : self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = isset($nameArr[1]) ? $nameArr[1] : self::PROVIDER_LASTNAME_PLACEHOLDER;
        } else {
            $userData['firstname'] = self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = self::PROVIDER_LASTNAME_PLACEHOLDER;
        }

        return $userData;
    }

    /**
     * @param $userData
     * @return mixed
     */
    protected function _setUserEmail($userData) {
        $userData['email'] = '';
        return $userData;
    }

    /**
     * @param $userdata
     * @return mixed
     */
    protected function _unsetUserData($userData) {
        foreach($this->_toUnset as $field) {
            unset($userData[$field]);
        }

        return $userData;
    }

    /**
     * @param array $userData
     */
    public function prepareUserProfileData($userData)
    {
        $userDataFields = [];

        $userDataFields['first_name'] = $this->_userData['firstname'];
        $userDataFields['last_name'] = $this->_userData['lastname'];
        $userDataFields['username'] = $userData['username'];
        $userDataFields['avatar'] = $userData['profile_picture'];
        $userDataFields['bio'] = '<p>' . $userData['bio'] . '</p>';

        $this->_userProfileData = $userDataFields;
    }


}

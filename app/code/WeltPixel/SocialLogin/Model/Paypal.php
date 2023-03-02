<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Paypal
 * @package WeltPixel\SocialLogin\Model
 */
class Paypal extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'paypal';

    /**
     * @var string
     */
    protected $_mode = '';

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
        $this->_mode = $this->getMode();
        $data = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'grant_type' =>  'authorization_code',
            'code' => $response,
        ];

        $apiToken = false;
        $headerArr = [
            'Authorization: Basic ' => base64_encode($this->_applicationId . ':' .$this->_secret)
        ];
        if ($response = $this->_apiCall($this->getApiTokenUrl(), $params, 'POST', null, $headerArr)) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
            if(isset($data['access_token'])){
                $token = $data['access_token'];
                $this->_setToken($token);

                $reqUrl = $this->getApiTokenRequestUrl();
                $this->_setCurlHeader();
                $apiDetails = $this->httpGet($reqUrl);
                $data = json_decode($apiDetails);

                $this->_setUserData($data);
            }
        }

        if (!$this->_userData = $this->_setSocialUserData($this->_userData)) {
            return false;
        }

        if ($this->isUserProfileCreationEnabled()) {
            $this->prepareUserProfileData($data);
        }

        return true;
    }

    /**
     * @param $userData
     */
    protected function _setUserData($userData) {
        $this->_userData['id'] = $userData->user_id;
        $this->_userData['email'] = $userData->email;
        $this->_setUserName($userData->name);
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
    protected function _setUserName($name) {
        if($name) {
            $nameArr = explode(' ', $name);
            $this->_userData['firstname'] = isset($nameArr[0]) ? $nameArr[0] : self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $this->_userData['lastname'] = isset($nameArr[1]) ? $nameArr[1] : self::PROVIDER_LASTNAME_PLACEHOLDER;
        } else {
            $userData['firstname'] = self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = self::PROVIDER_LASTNAME_PLACEHOLDER;
        }

        return $this->_userData;
    }

    /**
     * @return string
     */
    protected function getApiTokenUrl() {
        return 'https://api.'. $this->_mode .'.paypal.com/v1/identity/openidconnect/tokenservice';
    }

    /**
     * @return string
     */
    protected function getApiTokenRequestUrl() {
        return 'https://api.'. $this->_mode .'.paypal.com/v1/identity/oauth2/userinfo?schema=openid';
    }

    /**
     * @return string
     */
    public function getPaypalUrl()
    {
        if(!$this->_mode) {
            $this->_mode = $this->getMode();
        }
        $mode = ($this->_mode == 'sandbox') ? 'sandbox.' : '';
        return 'https://www.'.$mode.'paypal.com/signin/authorize?';
    }

    /**
     * @return string
     */
    protected function getMode() {
        $isSandBox = $this->_helper->getSocialConfig($this->_type, 'paypal_sandbox');
        return  $isSandBox ? 'sandbox' : '';
    }

    /**
     * @param array $userData
     */
    public function prepareUserProfileData($userData)
    {
        $userDataFields = [];

        $userDataFields['first_name'] = $this->_userData['firstname'];
        $userDataFields['last_name'] = $this->_userData['lastname'];
        $userDataFields['username'] = $this->_userData['firstname'] .'_' . $this->_userData['lastname'];

        $this->_userProfileData = $userDataFields;
    }


}

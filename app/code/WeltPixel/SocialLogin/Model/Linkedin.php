<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Linkedin
 * @package WeltPixel\SocialLogin\Model
 */
class Linkedin extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'linkedin';
    /**
     * @var string
     */
    protected $_apiTokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';
    /**
     * @var string
     */
    protected $_apiTokenRequestUrl = 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,email,profilePicture(displayImage~:playableStreams))';

    /**
     * @var string
     */
    protected $_apiTokenRequestUrlEmail = 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))';

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

        $data = $userData = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'grant_type' => 'authorization_code',
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        $headerArr = [
            'Authorization: Bearer ' . $response
        ];
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'POST', null, $headerArr)) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
            if (isset($data['access_token'])) {
                $token = $data['access_token'];
                $this->_setToken($token);

                $reqUrl = $this->_apiTokenRequestUrlEmail;
                $this->_setCurlHeader();
                $emailDetails = $this->httpGet($reqUrl);
                $emailData = json_decode($emailDetails, 1);
                $this->_setEmailData($emailData);

                $apiDetails = $this->httpGet($this->_apiTokenRequestUrl);
                $data = json_decode($apiDetails, 1);
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
     * @param array $userData
     */
    protected function _setUserData($userData)
    {
        $this->_userData['id'] = $userData['id'];
        if (isset($userData['firstName']['localized']) && is_array($userData['firstName']['localized'])) {
            $this->_userData['firstname'] = array_pop($userData['firstName']['localized']);
        } else {
            $this->_userData['firstname'] = self::PROVIDER_FIRSTNAME_PLACEHOLDER;
        }
        if (isset($userData['lastName']['localized']) && is_array($userData['lastName']['localized'])) {
            $this->_userData['lastname'] = array_pop($userData['lastName']['localized']);
        } else {
            $this->_userData['lastname'] = self::PROVIDER_LASTNAME_PLACEHOLDER;
        }
    }

    /**
     * @param array $emailData
     */
    protected function _setEmailData($emailData)
    {
        if (isset($emailData['elements'][0]['handle~']['emailAddress'])) {
            $this->_userData['email'] = $emailData['elements'][0]['handle~']['emailAddress'];
        }
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

        if (isset($userData['profilePicture']['displayImage~']['elements'][1]['identifiers']['0']['identifier'])) {
            $avatarImage = file_get_contents($userData['profilePicture']['displayImage~']['elements'][1]['identifiers']['0']['identifier']);
            $userDataFields['avatar'] = [
                'imageSrc' => $avatarImage,
                'imageUrl' => 'http://linkedin.com/linkedin_avatar_image.jpeg'
            ];
        }

        $this->_userProfileData = $userDataFields;
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
     * @param $object
     * @return mixed
     */
    protected function _object2array($object)
    {
        return json_decode(json_encode($object), 1);
    }


}

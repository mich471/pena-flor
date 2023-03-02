<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Fb
 * @package WeltPixel\SocialLogin\Model
 */
class Fb extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'fb';

    /**
     * @var string
     */
    protected $_url = 'https://www.facebook.com/dialog/oauth';

    /**
     * @var string
     */
    protected $_apiTokenUrl = 'https://graph.facebook.com/oauth/access_token';

    /**
     * @var string
     */
    protected $_apiGraphUrl = 'https://graph.facebook.com/me';

    /**
     * @var array
     */
    protected $_fields = [
        'user_id' => 'id',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'email' => 'email',
        'gender' => 'gender'
    ];

    protected $_userProfileFields = [
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'gender' => 'gender',
        'location' => 'location',
        'dob' => 'birthday',
        'avatar' => 'picture'
    ];

    protected $_avatarSrc = null;

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
        $apiReposonseData = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'GET')) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
        }

        if (isset($apiToken['access_token'])) {
            $params = [
                'access_token' => $apiToken['access_token'],
                'fields' => implode(',', array_unique(array_merge($this->_fields, $this->_userProfileFields)))
            ];

            if ($response = $this->_apiCall($this->_apiGraphUrl, $params, 'GET')) {
                $apiReposonseData = json_decode($response, true);

                foreach ($this->_fields as $key => $value) {
                    if (isset($apiReposonseData[$value])) {
                        $data[$value] = $apiReposonseData[$value];
                    }
                }
            }

            /** Get profile picture in bigger resolution */
            if ($this->isUserProfileCreationEnabled()) {
                $avatarImageUrl = $this->_apiGraphUrl . '/picture?type=large&access_token='. $apiToken['access_token'];
                $avatarImage = file_get_contents($avatarImageUrl);
                $this->_avatarSrc = $avatarImage;
            }
        }

        if (!$this->_userData = $this->_setSocialUserData($data)) {
            return false;
        }

        if ($this->isUserProfileCreationEnabled()) {
            $this->prepareUserProfileData($apiReposonseData);
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
     * @param array $apiReposonseData
     */
    public function prepareUserProfileData($apiReposonseData)
    {
        $userDataFields = [];
        foreach ($this->_userProfileFields as $key => $value) {
            if (isset($apiReposonseData[$value])) {
                switch ($value) {
                    case 'first_name':
                        $userDataFields['first_name'] = $this->_userData['firstname'];
                        break;
                    case 'last_name':
                        $userDataFields['last_name'] = $this->_userData['lastname'];
                        break;
                    case 'location':
                        $userDataFields[$key] = $apiReposonseData[$value]['name'];
                        break;
                    case 'gender':
                        if (isset($this->_userData['gender'])) {
                            $userDataFields[$key] = ($this->_userData['gender'] == 1) ? 'male' : 'female';
                        }
                        break;
                    case 'birthday':
                        $dateElements = explode("/",  $apiReposonseData[$value]);
                        if (count($dateElements) == 3) {
                            $userDataFields[$key] = $dateElements[2] . '-' . $dateElements[0] . '-' . $dateElements[1];
                        }
                        break;
                    case 'picture':
                        $userDataFields[$key] = $apiReposonseData[$value]['data']['url'];
                        break;
                }
            }
        }

        /** Set avatar image in bigger resolution */
        if (isset($this->_avatarSrc)) {
            $userDataFields['avatar'] = [
                'imageSrc' => $this->_avatarSrc,
                'imageUrl' => 'http://facebook.com/facebok_avatar_image.jpeg'
            ];
        }

        $userDataFields['username'] = $this->_userData['firstname'] .'_' . $this->_userData['lastname'];

        $this->_userProfileData = $userDataFields;
    }

}

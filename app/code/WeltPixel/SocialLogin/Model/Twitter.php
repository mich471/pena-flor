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
class Twitter extends \WeltPixel\SocialLogin\Model\Sociallogin
{

    const ENDPOINT_AUTH_URL = 'https://api.twitter.com/oauth/authorize';
    const ENDPOINT_REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';
    const ENDPOINT_ACCESS_TOKEN = 'https://api.twitter.com/oauth/access_token';
    const ENDPOINT_ACCOUNT_REQUEST = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    /**
     * @var string
     */
    protected $_type = 'twitter';

    /**
     * @var
     */
    protected $_oauthNonce;

    /**
     * @var
     */
    protected $_oauthTimestamp;

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
        if(empty($response['oauth_token']) || empty($response['oauth_verifier'])) {
            return false;
        }

        $data = [];

        $this->_setOauthNonce();
        $this->_setOauthTimestamp();
        $customerSession = $this->_objManager->create('Magento\Customer\Model\SessionFactory')->create();
        $oauthTokenSecret = $customerSession->getData('oauth_token_secret');
        if(empty($response['oauth_token']) || empty($response['oauth_verifier']) || !$oauthTokenSecret) {
            return false;
        }

        $oauthToken = $response['oauth_token'];
        $oauthVerifier = $response['oauth_verifier'];

        $url = self::ENDPOINT_ACCESS_TOKEN;
        $url .= '?oauth_nonce='.$this->_oauthNonce;
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$this->_oauthTimestamp;
        $url .= '&oauth_consumer_key='.$this->_applicationId;
        $url .= '&oauth_token='.urlencode($oauthToken);
        $url .= '&oauth_verifier='.urlencode($oauthVerifier);
        $url .= '&oauth_signature='.$this->_getOauthAccessSignature($oauthToken, $oauthVerifier, $oauthTokenSecret);
        $url .= '&oauth_version=1.0';

        $result = null;
        if($response = $this->_apiCall($url,[], 'GET')) {
            parse_str($response, $result);
        }

        if(!empty($result['oauth_token']) && !empty($result['oauth_token_secret'])) {
            $this->_setOauthNonce();
            $this->_setOauthTimestamp();

            $oauthToken = $result['oauth_token'];
            $oauthTokenSecret = $result['oauth_token_secret'];
            $screenName = $result['screen_name'];

            $url = self::ENDPOINT_ACCOUNT_REQUEST;
            $url .= "?include_email=true";
            $url .= '&oauth_consumer_key=' . $this->_applicationId;
            $url .= '&oauth_nonce=' . $this->_oauthNonce;
            $url .= '&oauth_signature=' . $this->_getOauthAccountSignature($oauthToken, $oauthTokenSecret, $screenName);
            $url .= '&oauth_signature_method=HMAC-SHA1';
            $url .= '&oauth_timestamp=' . $this->_oauthTimestamp;
            $url .= '&oauth_token=' . urlencode($oauthToken);
            $url .= '&oauth_version=1.0';
            $url .= '&screen_name=' . $screenName;

            $data = [];
            if($response = $this->_apiCall($url, [], 'GET')) {
                $data = json_decode($response, true);
            }

            if(isset($data['errors'])) {
                $customerSession = $this->_objManager->create('Magento\Customer\Model\SessionFactory')->create();
                $customerSession->setData('oauth_response_error', $data['errors'][0]['message']);
                return false;
            }
        }
        $this->_setUserData($data);

        if(!$this->_userData = $this->_setSocialUserData($this->_userData)) {
            return false;
        }


        if ($this->isUserProfileCreationEnabled()) {
            $this->prepareUserProfileData($data);
        }

        return true;
    }

    /**
     * @return mixed
     */
    protected function _requestOauthTokens()
    {
        $this->_setOauthNonce();
        $this->_setOauthTimestamp();

        $endpoint = self::ENDPOINT_REQUEST_TOKEN_URL;
        $endpoint .= '?oauth_callback='.urlencode($this->_redirectUri);
        $endpoint .= '&oauth_consumer_key='.$this->_applicationId;
        $endpoint .= '&oauth_nonce='.$this->_oauthNonce;
        $endpoint .= '&oauth_signature='.$this->_getOauthRequestSignature();
        $endpoint .= '&oauth_signature_method=HMAC-SHA1';
        $endpoint .= '&oauth_timestamp='.$this->_oauthTimestamp;
        $endpoint .= '&oauth_version=1.0';

        $endpointResponse = $this->_apiCall($endpoint, [], 'GET');
        if($endpointResponse) {
            parse_str($endpointResponse, $responseArr);
        }

        if(!empty($responseArr['oauth_token_secret'])) {

            $this->customerSession->setData('oauth_token_secret', $responseArr['oauth_token_secret']);
            $customerSession = $this->_objManager->create('Magento\Customer\Model\SessionFactory')->create();
            $customerSession->setData('oauth_token_secret', $responseArr['oauth_token_secret']);
        }

        return $responseArr;
    }

    /**
     * @return int
     */
    protected function _setOauthTimestamp() {
        $this->_oauthTimestamp = time();
    }

    /**
     * @return string
     */
    protected function _setOauthNonce() {
        $this->_oauthNonce = hash('SHA256',(uniqid(rand(), true)));
    }

    /**
     * @param $oauthNonce
     * @param $oauthTimestamp
     * @return string
     */
    protected function _getOauthRequestSignature() {
        $reqUri = "GET&";
        $reqUri .= urlencode(self::ENDPOINT_REQUEST_TOKEN_URL)."&";
        $reqUri .= urlencode("oauth_callback=".urlencode($this->_redirectUri)."&");
        $reqUri .= $this->commonSignatureParams();

        $secret = $this->_secret."&";
        $oauthSignature = $this->_hashSignature($reqUri, $secret);

        return $oauthSignature;
    }

    /**
     * @param $oauthToken
     * @param $oauthVerifier
     * @param $oauthTokenSecret
     * @return string
     */
    protected function _getOauthAccessSignature($oauthToken, $oauthVerifier, $oauthTokenSecret) {
        $reqUri = "GET&";
        $reqUri .= urlencode(self::ENDPOINT_ACCESS_TOKEN)."&";
        $reqUri .= urlencode("oauth_token=".$oauthToken."&");
        $reqUri .= urlencode("oauth_verifier=".$oauthVerifier."&");
        $reqUri .= $this->commonSignatureParams();

        $secret = $this->_secret .'&'. $oauthTokenSecret;
        $oauthSignature = $this->_hashSignature($reqUri, $secret);

        return $oauthSignature;
    }

    /**
     * @param $oauthToken
     * @param $oauthTokenSecret
     * @param $screenName
     * @return string
     */
    protected function _getOauthAccountSignature($oauthToken, $oauthTokenSecret, $screenName) {
        $reqUri = "GET&";
        $reqUri .= urlencode(self::ENDPOINT_ACCOUNT_REQUEST)."&";
        $reqUri .= urlencode("include_email=true&");
        $reqUri .= urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $reqUri .= urlencode("oauth_nonce=".$this->_oauthNonce.'&');
        $reqUri .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $reqUri .= urlencode("oauth_timestamp=".$this->_oauthTimestamp."&");
        $reqUri .= urlencode('oauth_token='.$oauthToken."&");
        $reqUri .= urlencode("oauth_version=1.0&");
        $reqUri .= urlencode('screen_name=' . $screenName);

        $secret = $this->_secret .'&'. $oauthTokenSecret;
        $oauthSignature = $this->_hashSignature($reqUri, $secret);

        return $oauthSignature;
    }

    /**
     * @return string
     */
    protected function commonSignatureParams() {
        $reqUri = urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $reqUri .= urlencode("oauth_nonce=".$this->_oauthNonce.'&');
        $reqUri .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $reqUri .= urlencode("oauth_timestamp=".$this->_oauthTimestamp."&");
        $reqUri .= urlencode("oauth_version=1.0");

        return $reqUri;
    }

    /**
     * @param $reqUri
     * @param $secret
     * @return string
     */
    protected function _hashSignature($reqUri, $secret) {
        $hash = base64_encode(hash_hmac('sha1', $reqUri, $secret, true));
        return urlencode($hash);
    }

    /**
     * @return string
     */
    public function getTwiterLink()
    {
        $link = '';
        $responseArr = $this->_requestOauthTokens();
        if(!empty($responseArr['oauth_token'])) {
            $link = self::ENDPOINT_AUTH_URL . '?oauth_token='. $responseArr['oauth_token'];
        }
        return $link;
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
     * @param $userData
     */
    protected function _setUserData($userData) {
        $this->_userData['id'] = $userData['id'];
        $this->_userData['email'] = $userData['email'];
        $this->_setUserName($userData['name']);
    }

    /**
     * @param array $userData
     */
    public function prepareUserProfileData($userData)
    {
        $userDataFields = [];

        $userDataFields['first_name'] = $this->_userData['firstname'];
        $userDataFields['last_name'] = $this->_userData['lastname'];
        $userDataFields['username'] = $userData['screen_name'];
        if (isset($userData['description'])) {
            $userDataFields['bio'] = '<p>' . $userData['description'] . '</p>';
        }

        if (isset($userData['profile_image_url_https'])) {
            $userDataFields['avatar'] = str_replace('_normal', '_200x200', $userData['profile_image_url_https']);
        }

        if (isset($userData['profile_banner_url'])) {
            $bannerImage = file_get_contents($userData['profile_banner_url']);
            $userDataFields['cover_image'] = [
                'imageSrc' => $bannerImage,
                'imageUrl' => 'http://twitter.com/twitter_cover_image.jpeg'
            ];
        }

        $this->_userProfileData = $userDataFields;
    }

}

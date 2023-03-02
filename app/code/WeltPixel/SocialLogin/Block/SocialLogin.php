<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block;


/**
 * Class SocialLogin
 * @package WeltPixel\SocialLogin\Block
 */
class SocialLogin extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $slHelper;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var array
     */
    protected $_socialMedia = [
        'fb' => 'https://www.facebook.com/dialog/oauth/?',
        'instagram' => 'https://api.instagram.com/oauth/authorize/?',
        'amazon' => 'https://www.amazon.com/ap/oa/?',
        'google' => 'https://accounts.google.com/o/oauth2/v2/auth?scope=email+profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+openid&access_type=offline&include_granted_scopes=true&state=state_parameter_passthrough_value&',
        'twitter' => 'https://api.twitter.com/oauth/request_token',
        'linkedin' => 'https://www.linkedin.com/oauth/v2/authorization?',
        'paypal' => 'https://www.paypal.com/signin/authorize?'
    ];
    /**
     * @var \WeltPixel\SocialLogin\Model\Twitter
     */
    protected $twitterModel;

    /**
     * @var \WeltPixel\SocialLogin\Model\Paypal
     */
    protected $paypalModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    protected $formKey;

    /**
     * SocialLogin constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \WeltPixel\SocialLogin\Helper\Data $slHelper
     * @param \WeltPixel\SocialLogin\Model\Twitter $twitterModel
     * @param \WeltPixel\SocialLogin\Model\Paypal $paypalModel
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \WeltPixel\SocialLogin\Model\Twitter $twitterModel,
        \WeltPixel\SocialLogin\Model\Paypal $paypalModel,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->slHelper = $slHelper;
        $this->_request = $request;
        $this->twitterModel = $twitterModel;
        $this->paypalModel = $paypalModel;
        $this->formKey = $formKey;
        $this->_customerUrl = $customerUrl;
        $this->_customerSession = $customerSession;
        if ($this->slHelper->isEnabled()) {
            $this->pageConfig->addBodyClass('wp-sl');
        }
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
            if (!$this->slHelper->isEnabled()) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getSkipModules()
    {
        $skip = $this->slHelper->getSkipModulesReferer();
        return json_encode($skip);
    }

    /**
     * @return bool|string
     */
    protected function _isCheckoutPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($route == 'checkout' && $controller == 'index') {
            return $this->getUrl('checkout/index/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    protected function _isCartPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($controller == 'cart' && $route == 'checkout') {
            return $this->getUrl('checkout/cart/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    public function getCurrentPageRedirectUrl() {
        $url = false;
        if($cartUrl = $this->_isCartPage()) {
            return $cartUrl;
        } elseif($checkoutUrl = $this->_isCheckoutPage()) {
            return $checkoutUrl;
        } else {
            return $url;
        }
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn() {
        return $this->slHelper->isCustomerLoggedIn();
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

}

<?php

namespace WeltPixel\Newsletter\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const COOKIE_NAME = 'weltpixel_newsletter';
    const COOKIE_NAME_EXITINTENT = 'weltpixel_exitintent';
    const COOKIE_NAME_SUBSCRIBED = 'weltpixel_newsletter_subscribed';
    const BLOCK_PREFIX = 'weltpixel_newsletter_v';
    const BLOCK_EXITINTENT_PREFIX = 'weltpixel_exitintent_newsletter_v';

    /**
     * \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var array
     */
    protected $_newsletterOptions;

    /** @var \WeltPixel\MobileDetect\Helper\Data */
    protected $_mobileDetectHelper;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscriber;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        parent::__construct($context);
        $this->_newsletterOptions = $this->scopeConfig->getValue('weltpixel_newsletter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_mobileDetectHelper = $mobileDetectHelper;
        $this->cookieHelper = $cookieHelper;
        $this->_subscriber= $subscriber;
        $this->_httpContext = $httpContext;
        $this->_customerSession = $customerSession;
        $this->_objManager = $objectManager;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return !$this->cookieHelper->isUserNotAllowSaveCookie() && $this->_newsletterOptions['general']['enable'];
    }

    /**
     * @return string
     */
    public function getOverlayColor()
    {
        return $this->_newsletterOptions['general']['overlay_color'];
    }

    /**
     * @return float
     */
    public function getOverlayOpacity()
    {
        return $this->_newsletterOptions['general']['overlay_opacity'];
    }

    /**
     * @return integer
     */
    public function getDisplayMode()
    {
        return $this->_newsletterOptions['general']['display_mode'];
    }

    /**
     * @return string
     */
    public function getMobileVersion()
    {
        return $this->_newsletterOptions['general']['mobile_version'];
    }

    /**
     * @return string
     */
    public function getDesktopVersion()
    {
        return $this->_newsletterOptions['general']['version'];
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        if ($this->displayOnMobile() && $this->_mobileDetectHelper->isMobile()) {
            return $this->getMobileVersion();
        }
        return $this->getDesktopVersion();
    }

    /**
     * @return string
     */
    public function getDisplayBlock()
    {
        $version = $this->getVersion();
        return self::BLOCK_PREFIX . $version;
    }

    /**
     * @return string
     */
    public function getDisplayBlockStep1()
    {
        $version = $this->getVersion();
        return self::BLOCK_PREFIX . $version . '_step_1';
    }


    /**
     * @return integer
     */
    public function getVisitedPages()
    {
        return $this->_newsletterOptions['general']['display_after_pages'];
    }

    /**
     * @return integer
     */
    public function getSecondsToDisplay()
    {
        return $this->_newsletterOptions['general']['display_after_seconds'];
    }

    /**
     * @return boolean
     */
    public function displayOnMobile()
    {
        return $this->_newsletterOptions['general']['display_mobile'];
    }

    /**
     * @return string
     */
    public function getCloseOption()
    {
        return $this->_newsletterOptions['general']['disable_popup'];
    }

    /**
     * @return integer
     */
    public function getLifeTime()
    {
        return $this->_newsletterOptions['general']['popup_cookie_lifetime'];
    }

    /**
     * @return string
     */
    public function getCookieName()
    {
        return self::COOKIE_NAME;
    }

    /**
     * @return string
     */
    public function getCookieNameSubscribed()
    {
        return self::COOKIE_NAME_SUBSCRIBED;
    }

    /**
     * @param bool $justCountPages
     * @return bool
     */
    public function canShowPopup($justCountPages = false)
    {
        $NisAjax = !$this->_request->isAjax();;
        $enabled = $this->isEnabled();
        $dOption = $this->getDisplayMode();
        //check if you are on home page
        $weAreOnHomePage = ($this->_getUrl('') == $this->_getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true))) ? 1 : 0;
        $displayOnMobile = $this->displayOnMobile();
        $canShowOnMobile = true;
        $isSubscribed = $this->isCustomerSubscribed();
        if (!$displayOnMobile && $this->_mobileDetectHelper->isMobile()) :
            $canShowOnMobile = false;
        endif;

        if (!$justCountPages) {
            if ($dOption == \WeltPixel\Newsletter\Model\Config\Source\DisplayMode::MODE_ALL_PAGES) {
                return ($enabled && $NisAjax && $canShowOnMobile && !$isSubscribed);
            } else {
                //check if you are on home page
                return ($enabled && $NisAjax && $weAreOnHomePage && $canShowOnMobile && !$isSubscribed);
            }
        } else {
            return ($enabled && $NisAjax && $canShowOnMobile && !$isSubscribed);
        }
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn() {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return bool
     */
    public function isCustomerSubscribed() {
       if(!$this->isCustomerLoggedIn()) {
           return false;
       }
       $customerSession = $this->_objManager->create('Magento\Customer\Model\SessionFactory')->create();
       $customerId = $customerSession->getCustomer()->getId();
       if(!$customerId) {
           return false;
       }

       $checkSubscriber = $this->_subscriber->loadByCustomerId($customerId);

       if($checkSubscriber->isSubscribed()) {
            return true;
       } else {
            return false;
       }
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->_getUrl('newsletter/subscriber/new', array('_secure' => true));
    }

    /**
     * @return boolean
     */
    public function isRequestAjax()
    {
        return $this->_getRequest()->isAjax();
    }

    /**
     * @return bool
     */
    public function isTermsConditionsEnabled()
    {
        return $this->_newsletterOptions['general']['terms_conditions_consent'];
    }

    /**
     * @return string
     */
    public function getTermsConditionsText()
    {
        return $this->_newsletterOptions['general']['terms_conditions_text'];
    }

    /**
     * @return bool
     */
    public function isTermsConditionsCheckboxRequired()
    {
        return $this->_newsletterOptions['general']['terms_conditions_checkbox'];
    }

    /**
     * @return int
     */
    public function getSignupSteps()
    {
        return $this->_newsletterOptions['general']['signup_steps'];
    }

    /**
     * @return string
     */
    public function getNewsletterSubmitButtonLabel()
    {
        return $this->_newsletterOptions['general']['newsletter_submit_button_label'];
    }

    /**
     * @return bool
     */
    public function isNewsletterCloseButtonEnabled()
    {
        return ((!in_array($this->getVersion(), [\WeltPixel\Newsletter\Model\Config\Source\Version::VERSION_1, \WeltPixel\Newsletter\Model\Config\Source\Version::VERSION_2])) && ($this->_newsletterOptions['general']['newsletter_close_button']));
    }


    /**
     * @return string
     */
    public function getNewsletterCloseButtonLabel()
    {
        return $this->_newsletterOptions['general']['newsletter_close_button_label'];
    }


    /**
     * @return string
     */
    public function getStep1ProceedButtonLabel()
    {
        return $this->_newsletterOptions['general']['signup_step_1_proceed_button_label'];
    }

    /**
     * @return bool
     */
    public function isStep1CloseButtonEnabled()
    {
        return $this->_newsletterOptions['general']['signup_step_1_close_button'];
    }

    /**
     * @return string
     */
    public function getStep1CloseButtonLabel()
    {
        return $this->_newsletterOptions['general']['signup_step_1_close_button_label'];
    }

    /**
     * @return bool
     */
    public function isTriggerButtonEnabled()
    {
        return $this->_newsletterOptions['general']['enable_trigger_button'];
    }

    /**
     * @return string
     */
    public function getTriggerButtonTitle()
    {
        return $this->_newsletterOptions['general']['trigger_button_title'];
    }

    /**
     * @return string
     */
    public function getTriggerButtonColor()
    {
        return $this->_newsletterOptions['general']['trigger_button_color'];
    }

    /**
     * @return string
     */
    public function getTriggerButtonBackgroundColor()
    {
        return $this->_newsletterOptions['general']['trigger_button_backgroundcolor'];
    }

    /**
     * @return bool
     */
    public function isExitIntentEnabled()
    {
        return !$this->cookieHelper->isUserNotAllowSaveCookie() && $this->_newsletterOptions['exitintent']['enable_exitintent'];
    }

    /**
     * @return string
     */
    public function getExitIntentCookieName()
    {
        return self::COOKIE_NAME_EXITINTENT;
    }

    /**
     * @return string
     */
    public function getExitIntentCloseOption()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_disable_popup'];
    }

    /**
     * @return bool
     */
    public function exitIntenDisplayUserSubscribed()
    {
        return (boolean)$this->_newsletterOptions['exitintent']['exitintent_display_user_subscribed'];
    }

    /**
     * @return bool
     */
    public function exitIntenDisplayClosedPopup()
    {
        return (boolean)$this->_newsletterOptions['exitintent']['exitintent_display_closed_popup'];
    }

    /**
     * @return integer
     */
    public function getExitIntentVersion()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_version'];
    }

    /**
     * @return string
     */
    public function getExitIntentOverlayColor()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_overlay_color'];
    }

    /**
     * @return float
     */
    public function getExitIntentOverlayOpacity()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_overlay_opacity'];
    }

    /**
     * @return bool
     */
    public function isExitIntentTermsConditionsEnabled()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_terms_conditions_consent'];
    }

    /**
     * @return string
     */
    public function getExitIntentTermsConditionsText()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_terms_conditions_text'];
    }

    /**
     * @return bool
     */
    public function isExitIntentTermsConditionsCheckboxRequired()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_terms_conditions_checkbox'];
    }

    /**
     * @return int
     */
    public function getExitIntentSignupSteps()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_signup_steps'];
    }

    /**
     * @return string
     */
    public function getExitIntentNewsletterSubmitButtonLabel()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_newsletter_submit_button_label'];
    }

    /**
     * @return bool
     */
    public function isExitIntentNewsletterCloseButtonEnabled()
    {
        return ((!in_array($this->getExitIntentVersion(), [\WeltPixel\Newsletter\Model\Config\Source\Version::VERSION_1, \WeltPixel\Newsletter\Model\Config\Source\Version::VERSION_2])) && ($this->_newsletterOptions['exitintent']['exitintent_newsletter_close_button']));
    }


    /**
     * @return string
     */
    public function getExitIntentNewsletterCloseButtonLabel()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_newsletter_close_button_label'];
    }


    /**
     * @return string
     */
    public function getExitIntentStep1ProceedButtonLabel()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_signup_step_1_proceed_button_label'];
    }

    /**
     * @return bool
     */
    public function isExitIntentStep1CloseButtonEnabled()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_signup_step_1_close_button'];
    }

    /**
     * @return string
     */
    public function getExitIntentStep1CloseButtonLabel()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_signup_step_1_close_button_label'];
    }

    /**
     * @return string
     */
    public function getExitIntentDisplayBlock()
    {
        $version = $this->getExitIntentVersion();
        return self::BLOCK_EXITINTENT_PREFIX . $version;
    }

    /**
     * @return string
     */
    public function getExitIntentDisplayBlockStep1()
    {
        $version = $this->getExitIntentVersion();
        return self::BLOCK_EXITINTENT_PREFIX . $version . '_step_1';
    }

    /**
     * @return bool
     */
    public function isPopupGtmTrackingEnabled()
    {
        return $this->_newsletterOptions['general']['popup_enable_gtm_tracking'] &&
            $this->_moduleManager->isEnabled('WeltPixel_GoogleTagManager') &&
            $this->scopeConfig->getValue('weltpixel_googletagmanager/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isExitIntentGtmTrackingEnabled()
    {
        return $this->_newsletterOptions['exitintent']['exitintent_enable_gtm_tracking'] &&
            $this->_moduleManager->isEnabled('WeltPixel_GoogleTagManager') &&
            $this->scopeConfig->getValue('weltpixel_googletagmanager/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSocialLoginIntegration()
    {
        return $this->scopeConfig->getValue('weltpixel_newsletter/sociallogin/sociallogin_integration', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSocialLoginAppliesTo()
    {
        return $this->scopeConfig->getValue('weltpixel_newsletter/sociallogin/sociallogin_integration_applies_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }
}

<?php

namespace WeltPixel\UserProfile\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\UserProfile\Model\UserProfile;
use WeltPixel\UserProfile\Model\UserProfileFields;

/**
 * Class AbstractWidget
 * @package WeltPixel\UserProfile\Block\Widget
 */
class AbstractWidget extends Template
{
    /**
     * @var array
     */
    protected $widgetOptions = [];

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var array
     */
    protected $formElementOptions = [];


    /**
     * @var UserProfile
     */
    protected $userProfile;

    /**
     * @var string
     */
    protected $formElementName = '';

    /**
     * AbstractWidget constructor.
     * @param UserProfileFields $userProfileFields
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        UserProfileFields $userProfileFields,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->userProfileFields = $userProfileFields;
        $this->formElementOptions = $this->userProfileFields->getFieldsOptions();
        $this->userProfile = null;
    }

    /**
     * @param $widgetOptions
     */
    public function setOptions($widgetOptions)
    {
        $this->widgetOptions = $widgetOptions;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        if (!isset($this->widgetOptions['required'])) {
            return false;
        }

        return (bool)$this->widgetOptions['required'];
    }

    /**
     * @return bool
     */
    public function isRequiredValidation()
    {
        return $this->isRequired();
    }


    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (!isset($this->widgetOptions['enabled'])) {
            return false;
        }

        return (bool)$this->widgetOptions['enabled'];
    }

    /**
     * @param UserProfile $userProfile
     * @return $this
     */
    public function initBlock($userProfile)
    {
        $this->userProfile = $userProfile;
        if (isset($this->formElementOptions[$this->formElementName])) {
            $this->setOptions($this->formElementOptions[$this->formElementName]);
        }
        return $this;
    }

    /**
     * @return null|UserProfile
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }

}

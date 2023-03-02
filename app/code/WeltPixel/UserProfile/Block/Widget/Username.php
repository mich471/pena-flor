<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Username
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Username extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'username';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/username.phtml');
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }
}

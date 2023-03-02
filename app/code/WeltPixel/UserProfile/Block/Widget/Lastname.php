<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Lastname
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Lastname extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'lastname';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/lastname.phtml');
    }
}

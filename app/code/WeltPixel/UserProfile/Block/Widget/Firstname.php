<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Firstname
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Firstname extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'firstname';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/firstname.phtml');
    }
}

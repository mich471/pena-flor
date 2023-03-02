<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Dob
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Dob extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'dob';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/dob.phtml');
    }

}

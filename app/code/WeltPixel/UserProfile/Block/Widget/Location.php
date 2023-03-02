<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Location
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Location extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'location';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/location.phtml');
    }
}

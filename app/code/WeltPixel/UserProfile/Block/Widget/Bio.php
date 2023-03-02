<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Bio
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Bio extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'bio';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/bio.phtml');
    }

}

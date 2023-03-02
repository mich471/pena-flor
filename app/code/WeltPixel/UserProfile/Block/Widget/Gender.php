<?php

namespace WeltPixel\UserProfile\Block\Widget;

use WeltPixel\UserProfile\Model\Field\Gender as GenderField;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\UserProfile\Model\UserProfileFields;

/**
 * Class Gender
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Gender extends AbstractWidget
{
    /**
     * @var GenderField
     */
    protected $genderField;

    /**
     * Gender constructor.
     * @param GenderField $genderField
     * @param UserProfileFields $userProfileFields
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        GenderField $genderField,
        UserProfileFields $userProfileFields,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($userProfileFields, $context, $data);
        $this->genderField = $genderField;
    }

    /**
     * @var string
     */
    protected $formElementName = 'gender';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/gender.phtml');
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->genderField->getOptions();
    }
}

<?php

namespace WeltPixel\UserProfile\Helper;

use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\UserProfile\Model\UserProfile;
use WeltPixel\UserProfile\Model\UserProfileFields;
use WeltPixel\UserProfile\Model\Field\Gender as GenderField;

/**
 * Class Renderer
 * @package WeltPixel\UserProfile\Helper
 */
class Renderer extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var array|null
     */
    protected $profileFieldOptions = null;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var GenderField
     */
    protected $genderField;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param UserProfileFields $userProfileFields
     * @param GenderField $genderField
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        UserProfileFields $userProfileFields,
        GenderField $genderField,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->userProfileFields = $userProfileFields;
        $this->genderField = $genderField;
        $this->storeManager = $storeManager;
    }


    /**
     * @param UserProfile $userProfile
     * @return string
     */
    public function getProfileName($userProfile)
    {

        $fieldOptions = $this->getProfileFieldOptions();
        $profileName = [];
        if ($fieldOptions['firstname']['enabled']) {
            $profileName[] = $userProfile->getFirstName();
        }

        if ($fieldOptions['lastname']['enabled']) {
            $profileName[] = $userProfile->getLastName();
        }


        return implode(" ", $profileName);
    }

    /**
     * @param UserProfile $userProfile
     * @return string
     */
    public function getProfileDetails($userProfile)
    {
        $details = [];
        $fieldOptions = $this->getProfileFieldOptions();

        if ($fieldOptions['gender']['enabled']) {
            $genderOption = $userProfile->getGender();
            if ($this->genderField->getOptionName($genderOption)) {
                $details[] = $this->genderField->getOptionName($genderOption);
            }
        }
        if ($fieldOptions['dob']['enabled']) {
            if ($userProfile->getDob()) {
                $details[] = $userProfile->getDob();
            }
        }

        return implode(", ", $details);
    }

    /**
     * @return array
     */
    public function getGenderOptions()
    {
        return $this->genderField->getOptions();
    }

    /**
     * @return array
     */
    protected function getProfileFieldOptions()
    {
        if (!$this->profileFieldOptions) {
            $this->profileFieldOptions = $this->userProfileFields->getFieldsOptions();
        }

        return $this->profileFieldOptions;
    }

    /**
     * @param $username
     * @return string
     */
    public function getUserProfileLink($username)
    {
        return $this->_getUrl('profile/user/' . $username, ['_secure' => true]);
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function getImageUrl($imagePath)
    {
        if ($imagePath) {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $mediaUrl . $imagePath;
        }
        return '';
    }
}

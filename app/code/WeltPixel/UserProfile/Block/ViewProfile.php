<?php

namespace WeltPixel\UserProfile\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\App\Http\Context as HttpContext;
use WeltPixel\UserProfile\Helper\Renderer as ProfileRendererHelper;
use WeltPixel\UserProfile\Model\UserProfile;
use WeltPixel\UserProfile\Model\UserProfileFields;
use Magento\Widget\Model\Template\Filter as TemplateFilter;

/**
 * Class ViewProfile
 * @package WeltPixel\UserProfile\Block
 */
class ViewProfile extends Template implements IdentityInterface
{
    /**
     * @var UserProfile
     */
    protected $userProfile = null;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var array|null
     */
    protected $profileFieldOptions = null;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var TemplateFilter
     */
    protected $templateFilter;

    /**
     * @var ProfileRendererHelper
     */
    protected $profileRendererHelper;


    /**
     * EditProfile constructor.
     * @param ProfileRendererHelper $profileRendererHelper
     * @param UserProfileFields $userProfileFields
     * @param HttpContext $httpContext
     * @param TemplateFilter $templateFilter
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProfileRendererHelper $profileRendererHelper,
        UserProfileFields $userProfileFields,
        HttpContext $httpContext,
        TemplateFilter $templateFilter,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->userProfileFields = $userProfileFields;
        $this->httpContext = $httpContext;
        $this->templateFilter = $templateFilter;
        $this->profileRendererHelper = $profileRendererHelper;
    }

    /**
     * @param UserProfile $userProfile
     * @return $this
     */
    public function setProfile($userProfile)
    {
        $this->userProfile = $userProfile;
        return $this;
    }

    /**
     * @return UserProfile
     */
    public function getProfile()
    {
        return $this->userProfile;
    }

    /**
     * @return string
     */
    public function getCoverImage()
    {
        if ($this->getProfile()->getCoverImage()) {
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $mediaUrl . $this->getProfile()->getCoverImage();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getAvatarImage()
    {
        if ($this->getProfile()->getAvatar()) {
            $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $mediaUrl . $this->getProfile()->getAvatar();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getProfileName()
    {
        $userProfile = $this->getProfile();
        return $this->profileRendererHelper->getProfileName($userProfile);
    }

    /**
     * @return string
     */
    public function getProfileDetails()
    {
        $userProfile = $this->getProfile();
        return $this->profileRendererHelper->getProfileDetails($userProfile);
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
     * @return bool
     */
    public function isCoverEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['cover_image']['enabled'];
    }

    /**
     * @return bool
     */
    public function isAvatarEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['avatar']['enabled'];
    }

    /**
     * @return bool
     */
    public function isNameEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)($fieldOptions['firstname']['enabled'] || $fieldOptions['lastname']['enabled']);
    }

    /**
     * @return bool
     */
    public function isFirstNameEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['firstname']['enabled'];
    }

    /**
     * @return bool
     */
    public function isLastNameEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['lastname']['enabled'];
    }

    /**
     * @return bool
     */
    public function isGenderEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['gender']['enabled'];
    }

    /**
     * @return array
     */
    public function getGenderOptions()
    {
        return $this->profileRendererHelper->getGenderOptions();
    }

    /**
     * @return bool
     */
    public function isDobEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['dob']['enabled'];
    }

    /**
     * @return bool
     */
    public function isDetailsEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)($fieldOptions['gender']['enabled'] || $fieldOptions['dob']['enabled']);
    }

    /**
     * @return bool
     */
    public function isLocationEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['location']['enabled'];
    }

    /**
     * @return bool
     */
    public function isBioEnabled()
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions['bio']['enabled'];
    }

    /**
     * @param string $fieldName
     * @return bool
     */
    public function isProfileFieldEnabled($fieldName)
    {
        $fieldOptions = $this->getProfileFieldOptions();
        return (bool)$fieldOptions[$fieldName]['enabled'];
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function getEditableVerificationUrl()
    {
        return $this->getUrl('profile/view/editable', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getUploadImageUrl()
    {
        return $this->getUrl('profile/view/uploadimage', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('profile/view/save', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getCustomerReviewsUrl()
    {
        return $this->getUrl('profile/view/customerreviews',
            [
                '_secure' => true,
                'id' => $this->getProfile()->getCustomerId()
            ]
        );
    }

    /**
     * @param bool $canEditWishlist
     * @return string
     */
    public function getCustomerWishlistsUrl($canEditWishlist = false)
    {
            return $this->getUrl('wp_collection/view/collections',
            [
                '_secure' => true,
                'id' => $this->getProfile()->getCustomerId(),
                'profileid' => $this->getProfile()->getProfileId(),
                'canEditWishlist' => $canEditWishlist
            ]
        );
    }

    /**
     * @param $content
     * @return string
     * @throws \Exception
     */
    public function filterHtmlOutput($content)
    {
        return $this->templateFilter->filter($content);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        $userProfile = $this->getProfile();
        if ($userProfile) {
            $identities[] = UserProfile::CACHE_TAG . '_' . $userProfile->getId();
        }
        return $identities;
    }
}

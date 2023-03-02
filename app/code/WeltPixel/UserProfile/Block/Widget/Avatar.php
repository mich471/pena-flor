<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class Avatar
 * @package WeltPixel\UserProfile\Block\Widget
 */
class Avatar extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'avatar';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/avatar.phtml');
    }

    /**
     * @return bool
     */
    public function isRequiredValidation()
    {
        if (strlen(trim($this->getUserProfile()->getAvatar()))) {
            return false;
        }

        return parent::isRequiredValidation();
    }

    /**
     * @return string
     */
    public function getPreviewImage()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . $this->getUserProfile()->getAvatar();
    }

}

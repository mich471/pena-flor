<?php

namespace WeltPixel\UserProfile\Block\Widget;

/**
 * Class CoverImage
 * @package WeltPixel\UserProfile\Block\Widget
 */
class CoverImage extends AbstractWidget
{
    /**
     * @var string
     */
    protected $formElementName = 'cover_image';

    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('WeltPixel_UserProfile::widget/coverimage.phtml');
    }

    /**
     * @return bool
     */
    public function isRequiredValidation()
    {
        if (strlen(trim($this->getUserProfile()->getCoverImage()))) {
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
        return $mediaUrl . $this->getUserProfile()->getCoverImage();
    }

}

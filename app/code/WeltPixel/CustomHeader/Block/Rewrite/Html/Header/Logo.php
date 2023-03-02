<?php


namespace WeltPixel\CustomHeader\Block\Rewrite\Html\Header;


class Logo extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'WeltPixel_CustomHeader::html/logo.phtml';

    /**
     * Get logo image URL
     *
     * @param string $path
     * @return string
     */
    public function getTypeLogoSrc(string $path)
    {
        if (empty($this->_data['mobile_logo_src'])) {
            $this->_data['mobile_logo_src'] = $this->getTypeLogoUrl($path);
        }
        return $this->_data['mobile_logo_src'];
    }

    /**
     * Retrieve logo image URL
     *
     * @param string $path
     * @return string
     */
    protected function getTypeLogoUrl(string $path)
    {
        $folderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.svg');
        }
        return $url;
    }

    /**
     * Retrieve logo text
     *
     * @return string
     */
    public function getMobileLogoAlt()
    {
        if (empty($this->_data['mobile_logo_alt'])) {
            $this->_data['mobile_logo_alt'] = $this->_scopeConfig->getValue(
                'design/header/mobile_logo_alt',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_data['mobile_logo_alt'];
    }

    /**
     * Retrieve logo width
     *
     * @return int
     */
    public function getMobileLogoWidth()
    {
        if (empty($this->_data['mobile_logo_width'])) {
            $this->_data['mobile_logo_width'] = $this->_scopeConfig->getValue(
                'design/header/mobile_logo_width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return (int)$this->_data['mobile_logo_width'];
    }

    /**
     * Retrieve logo height
     *
     * @return int
     */
    public function getMobileLogoHeight()
    {
        if (empty($this->_data['mobile_logo_height'])) {
            $this->_data['mobile_logo_height'] = $this->_scopeConfig->getValue(
                'design/header/mobile_logo_height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return (int)$this->_data['mobile_logo_height'];
    }

    /**
     * Retrieve mobile logo position
     *
     * @return string
     */
    public function getMobilePosition()
    {
        if (empty($this->_data['mobile_logo_position'])) {
            $this->_data['mobile_logo_position'] = $this->_scopeConfig->getValue(
                'design/header/mobile_logo_position',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return (string)$this->_data['mobile_logo_position'];
    }

    /**
     * Retrieve logo image
     *
     * @return string
     */
    public function checkMobileLogoUploaded()
    {
        if (empty($this->_data['mobile_header_logo_src'])) {
            $this->_data['mobile_header_logo_src'] = $this->_scopeConfig->getValue(
                'design/header/mobile_header_logo_src',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_data['mobile_header_logo_src'];
    }

}

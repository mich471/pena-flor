<?php

namespace Adwise\ModalNewsletter\Block;

use Magento\Store\Model\ScopeInterface;

class Subscribe extends \Magento\Framework\View\Element\Template
{

    protected $scopeConfig;

    protected $_storeManagerInterface;

    const PATCH_MODULE = 'newsletter_modal/general/';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context   $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface         $storeManagerInterface,
        array                                              $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
        $this->_storeManagerInterface = $storeManagerInterface;
    }

    public function getBlockId()
    {
        return $this->scopeConfig->getValue(self::PATCH_MODULE . 'block_modal', ScopeInterface::SCOPE_WEBSITE);
    }

    public function isRecatcha()
    {
        return $this->scopeConfig->getValue(self::PATCH_MODULE . 'recaptcha', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getWebsiteKeyRecaptcha()
    {
        return $this->scopeConfig->getValue('msp_securitysuite_recaptcha/general/public_key');
    }


    public function getTimeCookies()
    {
        return (int) $this->scopeConfig->getValue(self::PATCH_MODULE . 'cookis', ScopeInterface::SCOPE_WEBSITE);
    }


    public function getText()
    {
        return $this->scopeConfig->getValue(self::PATCH_MODULE . 'text_poppup', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getImageNewsletter()
    {
        $imageConfig = $this->scopeConfig->getValue(self::PATCH_MODULE . 'image_newsletter', ScopeInterface::SCOPE_WEBSITE);
        return $this->getPathImage($imageConfig);
    }

    private function getPathImage($image)
    {
        $store = $this->_storeManagerInterface->getStore();
        $storeMedia = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'newsletter/' . $image;
        return $storeMedia;
    }
}

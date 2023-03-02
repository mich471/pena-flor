<?php
namespace WeltPixel\GoogleCards\Plugin;

class ConfigView {

    const XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_WIDTH = 'weltpixel_google_cards/general/product_image_width';
    const XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_HEIGHT = 'weltpixel_google_cards/general/product_image_height';
    const XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_TYPE = 'weltpixel_google_cards/general/product_image_type';

    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Config\View $subject
     * @param \Closure $proceed
     * @param string $module
     * @param string $mediaType
     * @param string $mediaId
     * @return array
     */
    public function aroundGetMediaAttributes(
        \Magento\Framework\Config\View $subject,
        \Closure $proceed,
        $module,
        $mediaType,
        $mediaId)
    {
        $result = $proceed($module, $mediaType, $mediaId);

        switch ($mediaId) {
            case "weltpixel_googlecards_product_image" :
                $imageWidth = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_WIDTH,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                $imageHeight = trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_HEIGHT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
                $imageType = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_TYPE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                if (strlen($imageWidth)) {
                    $result['width'] = (int)$imageWidth;
                }
                if (strlen($imageHeight)) {
                    $result['height'] = (int)$imageHeight;
                }
                $result['type'] = $imageType;
                break;
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Config\View $subject
     * @param array $result
     * @param string $module
     * @param string $mediaType
     * @return array
     */
    public function afterGetMediaEntities(\Magento\Framework\Config\View $subject, array $result, $module, $mediaType)
    {
        $result['weltpixel_googlecards_product_image'] = [
            'width' => trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_WIDTH,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
            'height' => trim($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_HEIGHT,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
            'type' => $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_GOOGLECARDS_IMAGE_TYPE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
        return $result;
    }
}

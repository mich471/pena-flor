<?php

namespace WeltPixel\ProductPage\Plugin;

class BlockProductViewTypeConfigurable
{
    const XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_STRATEGY_CONFIGURABLE_PRODUCTS = 'weltpixel_product_page/gallery/strategy_configurable_products';

    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param \Closure $proceed
     * @param string $name
     * @param string|null $module
     * @return string|false
     */
    public function aroundGetVar(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed,
        $name,
        $module = null
    ) {
        $result = $proceed($name, $module);

        switch ($name) {
            case "gallery_switch_strategy":
                $result = $this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_PRODUCTPAGE_GALLERY_STRATEGY_CONFIGURABLE_PRODUCTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
        }

        return $result;
    }
}

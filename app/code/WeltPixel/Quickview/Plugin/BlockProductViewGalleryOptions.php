<?php

namespace WeltPixel\Quickview\Plugin;

class BlockProductViewGalleryOptions
{

    const XML_PATH_WELTPIXEL_QUICKVIEW_MAGNIFIER_ENABLED = 'weltpixel_quickview/general/enable_zoom';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var  \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * ResultPage constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\GalleryOptions $subject
     * @param \Closure $proceed
     * @param string $name
     * @param string|null $module
     * @return string|false
     */
    public function aroundGetVar(
        \Magento\Catalog\Block\Product\View\GalleryOptions $subject,
        \Closure $proceed,
        $name,
        $module = null
    )
    {
        $result = $proceed($name, $module);

        if ($this->request->getFullActionName() != 'weltpixel_quickview_catalog_product_view') {
            return $result;
        }

        if ($name == "gallery/allowfullscreen") {
            $result = filter_var($this->scopeConfig->getValue(self::XML_PATH_WELTPIXEL_QUICKVIEW_MAGNIFIER_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), FILTER_VALIDATE_BOOLEAN);
        }

        return $result;
    }


}

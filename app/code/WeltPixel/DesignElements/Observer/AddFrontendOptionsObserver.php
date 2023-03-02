<?php
namespace WeltPixel\DesignElements\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddFrontendOptionsObserver implements ObserverInterface
{
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;
    /**
    * @var \Magento\Framework\App\Request\Http
    */
    protected $httpRequest;


    const XML_PATH_DESIGN_ELEMENTS_BOOTSTRAP_GRID = 'weltpixel_design_elements/general/bootstrap_grids';
    const XML_PATH_DESIGN_ELEMENTS_ICONS_CORE = 'weltpixel_design_elements/general/icons_core';
    const XML_PATH_DESIGN_ELEMENTS_ICONS_EXTENDED = 'weltpixel_design_elements/general/icons_extended';
    const XML_PATH_DESIGN_ELEMENTS_BOOTSTRAP_TABLES = 'weltpixel_design_elements/general/bootstrap_tables';
    const XML_PATH_DESIGN_ELEMENTS_TOGGLES_ACCORDIONS_TABS = 'weltpixel_design_elements/general/toggles_accordions_tabs';
    const XML_PATH_DESIGN_ELEMENTS_PRICING_BOXES = 'weltpixel_design_elements/general/pricing_boxes';
    const XML_PATH_DESIGN_ELEMENTS_HEADINGS_BLOCKQUOTES = 'weltpixel_design_elements/general/headings_blockquotes';
    const XML_PATH_DESIGN_ELEMENTS_DIVIDERS = 'weltpixel_design_elements/general/dividers';
    const XML_PATH_DESIGN_ELEMENTS_BRAND_LISTS = 'weltpixel_design_elements/general/brand_lists';
    const XML_PATH_DESIGN_ELEMENTS_RESPONSIVE_HELPERS = 'weltpixel_design_elements/general/responsive_helpers';
    const XML_PATH_DESIGN_ELEMENTS_SMOOTH_SCROLLING = 'weltpixel_design_elements/general/smooth_scrolling';
    const XML_PATH_DESIGN_ELEMENTS_CORE_ICON_BOXES = 'weltpixel_design_elements/general/icon_boxes_core';
    const XML_PATH_DESIGN_ELEMENTS_EXTENDED_ICON_BOXES = 'weltpixel_design_elements/general/icon_boxes_extended';
    const XML_PATH_DESIGN_ELEMENTS_ALERT_BOXES = 'weltpixel_design_elements/general/alert_boxes';
    const XML_PATH_DESIGN_ELEMENTS_TESTIMONIALS = 'weltpixel_design_elements/general/testimonials';
    const XML_PATH_DESIGN_ELEMENTS_FLEXSLIDER = 'weltpixel_design_elements/general/flexslider';
    const XML_PATH_DESIGN_ELEMENTS_BUTTONS = 'weltpixel_design_elements/general/buttons';
    const XML_PATH_DESIGN_ELEMENTS_SECTIONS = 'weltpixel_design_elements/general/sections';
    const XML_PATH_DESIGN_ELEMENTS_PARALLAX = 'weltpixel_design_elements/general/parallax';
    const XML_PATH_DESIGN_ELEMENTS_ANIMATIONS_CORE = 'weltpixel_design_elements/general/animations_core';
    const XML_PATH_DESIGN_ELEMENTS_ANIMATIONS_EXTENDED = 'weltpixel_design_elements/general/animations_extended';
    const XML_PATH_DESIGN_ELEMENTS_AOS_ANIMATION = 'weltpixel_design_elements/general/aos_animation';
    const XML_PATH_DESIGN_ELEMENTS_BTT_BUTTON = 'weltpixel_design_elements/general/btt_button';
    const XML_PATH_DESIGN_ELEMENTS_CALENDAR_CSS = 'weltpixel_design_elements/general/calendar_css';
    const XML_PATH_DESIGN_ELEMENTS_BTT_BUTTON_PRODUCTPAGE = 'weltpixel_design_elements/btt_product_page/enable';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $httpRequest)
    {
        $this->scopeConfig = $scopeConfig;
        $this->httpRequest = $httpRequest;
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        $includeBootstrapGrid = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BOOTSTRAP_GRID,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeFontsCore = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_ICONS_CORE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeFontsExtended = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_ICONS_EXTENDED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeBootstrapTables = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BOOTSTRAP_TABLES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeTogglesAccordionsabs = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_TOGGLES_ACCORDIONS_TABS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includePricingBoxes = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_PRICING_BOXES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeHeadingsBlockQuotes = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_HEADINGS_BLOCKQUOTES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeDividers = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_DIVIDERS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeBrandLists = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BRAND_LISTS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeResponsiveHelpers = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_RESPONSIVE_HELPERS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeSmoothScrolling = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_SMOOTH_SCROLLING,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeCoreIconBoxes = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_CORE_ICON_BOXES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeExtendedIconBoxes = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_EXTENDED_ICON_BOXES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeAlertBoxes = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_ALERT_BOXES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeTestimonials = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_TESTIMONIALS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeFlexSlider = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_FLEXSLIDER,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeButtons = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BUTTONS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeSections = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_SECTIONS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeParallax = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_PARALLAX,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeAnimationsCore = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_ANIMATIONS_CORE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeAnimationsExtended = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_ANIMATIONS_EXTENDED,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeAOSAnimation = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_AOS_ANIMATION,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeBttButton = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BTT_BUTTON,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeCalendarCss = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_CALENDAR_CSS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $includeProductPageBttButton = $this->scopeConfig->getValue(self::XML_PATH_DESIGN_ELEMENTS_BTT_BUTTON_PRODUCTPAGE,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($includeBootstrapGrid) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_bootstrap_grid');
        }
        if ($includeFontsCore) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_fonts_core');
        }
        if ($includeFontsExtended) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_fonts_extended');
        }
        if ($includeBootstrapTables) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_bootstrap_tables');
        }
        if ($includeTogglesAccordionsabs) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_toggles_accordions_tabs');
        }
        if ($includePricingBoxes) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_pricing_boxes');
        }
        if ($includeHeadingsBlockQuotes) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_headings_blockquotes');
        }
        if ($includeDividers) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_dividers');
        }
        if ($includeBrandLists) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_brand_lists');
        }
        if ($includeResponsiveHelpers) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_responsive_helpers');
        }
        if ($includeSmoothScrolling) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_smooth_scrolling');
        }
        if ($includeCoreIconBoxes) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_core_icon_boxes');
        }
        if ($includeExtendedIconBoxes) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_extended_icon_boxes');
        }
        if ($includeAlertBoxes) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_alert_boxes');
        };
        if ($includeTestimonials) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_testimonials');
        }
        if ($includeFlexSlider) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_flexslider');
        }
        if ($includeButtons) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_buttons');
        }
        if ($includeSections) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_sections');
        }
        if ($includeParallax) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_parallax');
        }
        if ($includeAnimationsCore) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_animations_core');
        }
        if ($includeAnimationsExtended) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_animations_extended');
        }
        if ($includeAOSAnimation) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_aos_animation');
        }
        if (!$includeCalendarCss) {
            $layout->getUpdate()->addHandle('weltpixel_design_elements_remove_calendar_css');
        }
        if ($includeBttButton) {
            $layoutHandle = 'weltpixel_design_elements_btt_button';
            if  ($includeProductPageBttButton && $this->httpRequest->getFullActionName() == 'catalog_product_view') {
                $layoutHandle = 'weltpixel_design_elements_btt_button_product';
            }
            $layout->getUpdate()->addHandle($layoutHandle);
        }

        if($this->httpRequest->getFullActionName() == 'cms_index_index' || $this->httpRequest->getFullActionName() == 'cms_page_view'){
            $layout->getUpdate()->addHandle('weltpixel_cms_page_view');
        }

        return $this;
    }
}

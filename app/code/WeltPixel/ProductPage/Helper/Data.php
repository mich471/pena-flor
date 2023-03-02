<?php

namespace WeltPixel\ProductPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAGE_VERSION_NO_GALLERY = [2, 4];
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \WeltPixel\MobileDetect\Helper\Data
     */
    protected $mobileDetectHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->mobileDetectHelper = $mobileDetectHelper;
        $this->request = $request;
        $this->priceHelper = $priceHelper;
        $this->rule = $rule;
        $this->serializer = $serializer;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function productVersion($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/version/version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function moveDescriptionsTabs($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/move_description_tabs_under_info_area', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getPositionProductInfo($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/position_product_info', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeWishlist($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/remove_wishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeCompare($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/remove_compare', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getImageAreaWidth($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/image_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getProductInfoAreaWidth($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/product_info_area_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function removeSwatchTooltip($storeId = null)
    {
        return !$this->scopeConfig->getValue('weltpixel_product_page/general/display_swatch_tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getTabsLayout($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getTabsVersion($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getAccordionVersion($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/accordion_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isAccordionCollapsible($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/accordion_collapsible', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getQtyType($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getQtySelectMaxValue($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_select_maxvalue', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getSwatchOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/swatch', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCssOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBackgroundArrows($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/gallery/arrows_bg', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGalleryNavDir($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/gallery/navdir', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getMagnifierEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/magnifier/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getMagnifierEventType($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/magnifier/eventtype', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->request->getParam('id');
    }

    /**
     * @return string
     */
    public function getQtyTemplate()
    {
        $template = 'WeltPixel_ProductPage::product/view/addtocart_default.phtml';
        $qtyType = $this->getQtyType();
        switch ($qtyType) {
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_DEFAULT:
                $template = 'WeltPixel_ProductPage::product/view/addtocart_default.phtml';
                break;
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_SELECT:
                $template = 'WeltPixel_ProductPage::product/view/addtocart.phtml';
                break;
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_PLUS_MINUS:
                $template = 'WeltPixel_ProductPage::product/view/addtocart_plus_minus.phtml';
                break;
        }

        return $template;
    }

    public function getQtyTemplateForCartEdit()
    {
        $template = 'WeltPixel_ProductPage::cart/item/configure/updatecart_default.phtml';
        $qtyType = $this->getQtyType();
        switch ($qtyType) {
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_DEFAULT:
                $template = 'WeltPixel_ProductPage::cart/item/configure/updatecart_default.phtml';
                break;
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_SELECT:
                $template = 'WeltPixel_ProductPage::cart/item/configure/updatecart.phtml';
                break;
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_PLUS_MINUS:
                $template = 'WeltPixel_ProductPage::cart/item/configure/updatecart_plus_minus.phtml';
                break;
        }

        return $template;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAddToCartBtnPosition($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_add_to_cart_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isVisitorCounterEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getVisitorCounterSessionIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/session_identifier', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getVisitorCounterUpdateFrequency($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/update_frequency', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getVisitorCounterRefreshDelay($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/refresh_delay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getVisitorCounterIntervalCheck($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/realtime_update_frequency', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getVisitorCounterDisplayText($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/display_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getVisitorCounterDisplayLimit($storeId = null)
    {
        return (int)$this->scopeConfig->getValue('weltpixel_product_page/visitor_counter/display_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return boolean
     */
    public function isPrevNextEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/prevnext/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPrevNextProductName($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/prevnext/productname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isSaleMessageEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/sale_message/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getSaleMessageContent($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/sale_message/content', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param float $specialPrice
     * @param float $regularPrice
     * @return string
     */
    public function getSaleOffMessage($specialPrice, $regularPrice)
    {
        $isSaleMessageEnabled = $this->isSaleMessageEnabled();
        if (!$isSaleMessageEnabled) {
            return '';
        }

        $discountPercent =  100 - round(($specialPrice * 100) / $regularPrice);
        $discountValue = $this->priceHelper->currency($regularPrice - $specialPrice, true, false);
        $saleMessageContent = $this->getSaleMessageContent();

        $parsedSaleMessage = str_replace([
            '{discount_percent}',
            '{discount_value}'
            ], [
            '<span id="wp-discount-percent">' . $discountPercent . '</span>%',
            '<span id="wp-discount-value">' . $discountValue . '</span>'
            ], $saleMessageContent);

        return $parsedSaleMessage;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMobileThreshold($storeId = null)
    {
        $mobileTreshold = (int) $this->scopeConfig->getValue(
            'weltpixel_frontend_options/breakpoints/screen__m',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return ($mobileTreshold) ? $mobileTreshold : 768;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isStickyAddToCartDesktopEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/enable_desktop', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isStickyAddToCartMobileEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/enable_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getDisplayStickyAddToCartOnDesktopOptions($storeId = null)
    {
        return explode(",", $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/display_on_desktop', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getDisplayStickyAddToCartOnMobileOptions($storeId = null)
    {
        return explode(",", $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/display_on_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSizeChartCustomCmsBlock($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/size_chart/size_chart_custom_cms', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSizeChartConditions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/size_chart/size_chart_conditions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSizeChartAvailableForProduct($product)
    {
        $conditions = $this->getSizeChartConditions();
        try {
            $conditions = $this->serializer->unserialize($conditions);
        } catch (\Exception $ex) {
            $conditions = [];
        }

        $this->rule->loadPost($conditions);
        return $this->rule->getConditions()->validate($product);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSizeChartCustomLabel($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/size_chart/size_chart_custom_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCurrentCustomHeaderVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/general/header_style', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getQtySelectorBorderRadius($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/qty_plus_minus_border_radius', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function stickyHeaderWhenScrollUpIsEnabled($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/sticky_header/enable_sticky_scroll_up', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTopHeaderWidthForSticky($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_custom_header/bottom_header/width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyCartDisplayMode($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/sticky_cart_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyCartDisplayModeMobile($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/sticky_addtocart/sticky_cart_mode_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyTabsAlignment($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_alignment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStickyTabsBarColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/tabs_bar_background_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getProductPageWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/product_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getProductPagePadding($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/page_main_padding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function isViewMoreLessListEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/view_more_less_list', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    public function getViewMoreLessHeight($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_product_page/general/view_more_less_height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}

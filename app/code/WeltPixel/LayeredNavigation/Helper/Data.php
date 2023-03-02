<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Helper;

use Magento\Store\Model\ScopeInterface;
use \Magento\CatalogSearch\Model\ResourceModel\EngineInterface;


/**
 * Class Data
 * @package WeltPixel\LayeredNavigation\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const SLIDE_IN_STYLE = 1;
    const SLIDE_DOWN_STYLE = 2;
    const FILTER_BUTTON_LABEL = 'Filter';
    const PRICE_FILTER_SLIDER = 2;
    const PRICE_FILTER_INPUT = 1;
    const CATEGORY_PARAM_LABEL = 'cat';
    const RATING_PARAM_LABEL = 'rat';

    protected $_currentEngine = '';


     /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig
    )
    {
        $this->_storeManager = $storeManager;
        $this->pageConfig = $pageConfig;
        parent::__construct($context);

        $this->_storeId = $this->getCurrentStoreId();
    }

    /**
     * @return string
     */
    public function getRatingParamLabel() {
        return self::RATING_PARAM_LABEL;
    }

    /**
     * @return string
     */
    public function getCategoryParamLabel() {
        return self::CATEGORY_PARAM_LABEL;
    }

    /**
     * Return current store_id
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isAjaxEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/general/ajax', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSidebarStyle($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/sb_style', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getNoColumns($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/slide_down_columns', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSlideDownHeight($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/slide_down_height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFilterColumnHeight($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/filter_column_height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function showCategoriesBlock($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/category_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function showCompareBlock($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/compare_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function showWishlistBlock($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/wishlist_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function showRecentlyOrderedBlock($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/recentlyordered_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getFilterButton($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/filter_button', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function showInstantSearch($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/instant_search', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function showInstantSearchMobile($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/instant_search_mobile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * Filter Button option
     *  0-icon only
     *  1-icon & label
     *  2-label only
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getFilterButtonStyle()
    {

        $slideDownModeAppend = ($this->getSidebarStyle() == self::SLIDE_DOWN_STYLE) ? "<span class='wp-slide-down-add'></span>" : '';

        switch ($this->getFilterButton()) {
            case 0:
                $filterButtonHtml =  "<b class='wp-slide-in'></b>" . $slideDownModeAppend;
                break;
            case 1:
                $filterButtonHtml = "<b class='wp-slide-in'></b><b class='wp-filters-text'>" . /* @escapeNotVerified */ __(self::FILTER_BUTTON_LABEL) . "</b>" . $slideDownModeAppend;
                break;
            case 2:
                $filterButtonHtml = __(self::FILTER_BUTTON_LABEL);
                break;
            default:
                $filterButtonHtml = '';
        }

        return $filterButtonHtml;
    }

    /**
     * @return bool
     */
    public function getSlideMode() {
        if($this->getSidebarStyle() == self::SLIDE_IN_STYLE) {
            return 'slider-layer';
        } else if($this->getSidebarStyle() == self::SLIDE_DOWN_STYLE) {
            return 'slider-down-layer';
        }

        return false;
    }

    /**
     * update class on body tag based on configuration settings
     * the class is used for layered navigation view mode setting
     */
    public function updateSliderBodyClass() {
        if($this->getSlideMode()) {
            $this->pageConfig->addBodyClass($this->getSlideMode());
        } else {
            $this->pageConfig->addBodyClass('default-layer');
        }

        return;
    }

    /**
     * Check the engine provider is 'elasticsearch'
     *
     * @return bool
     */
    public function isElasticSearchEngine() {
        if(!$this->_currentEngine) {
            $this->_currentEngine = $this->scopeConfig->getValue(EngineInterface::CONFIG_ENGINE_PATH, ScopeInterface::SCOPE_STORE);
        }
        if($this->_currentEngine == 'elasticsearch' || $this->_currentEngine == 'elasticsearch5' || $this->_currentEngine == 'elasticsearch6'
            || $this->_currentEngine == 'elasticsearch7') {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPriceRangeStep() {
        return $this->scopeConfig->getValue('catalog/layered_navigation/price_range_step', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPriceFilterStyle($storeId = null) {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function getPriceIsSliderMode() {
        $sliderMode = false;
        if($this->getPriceFilterStyle() == self::PRICE_FILTER_SLIDER) {
            $sliderMode = true;
        }

        return $sliderMode;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getPriceFilterInput($storeId = null) {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/price_filter_input', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function canShowPriceInput() {
        $isInput = false;
        $isInputSet = ($this->getPriceFilterInput() == self::PRICE_FILTER_INPUT) ? true : false;
        if($this->getPriceIsSliderMode() && $isInputSet) {
            $isInput = true;
        }

        return $isInput;

    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isRatingFilterEnabled($storeId = null) {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @return bool|mixed
     */
    public function getRatingFilterName($storeId = null) {
        $name = false;
        if($this->isRatingFilterEnabled() && $this->isEnabled()) {
            $name = $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }

        return $name;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRatingFilterPosition($storeId = null) {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isRatingFilterMultiselect($storeId = null) {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter_multiselect', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRatingFilterDisplay($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter_display', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRatingFilterCounter($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/rating_filter_counter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isAutoClose($storeId = null)
    {

        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/auto_close_sidebar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isShowHideDesktopFilterEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/allow_layered_show_hide', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getShowHideDesktopFilterState($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/layered_show_hide_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalSidebarDesignVersion($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_sb_design_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalSidebarFilterPosition($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_sb_design_v2_filter_position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function isHorizontalSidebarStickyEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_sb_sticky_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getSelectedFiltersOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/sb_selected_filters_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMobileThreshold($storeId = null)
    {
        $mobileThreshold = (int) $this->scopeConfig->getValue(
            'weltpixel_frontend_options/breakpoints/screen__m',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return ($mobileThreshold) ? $mobileThreshold : 768;
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalV3FiltersButtonColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_filters_button_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalV3FiltersTextColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_filters_text_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalV3FiltersButtonHoverColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_filters_button_hover_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalV3FiltersTextHoverColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_filters_text_hover_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalFiltersBorder($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_border', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalFiltersBorderRadius($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_border_radius', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalFiltersBorderColor($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_v3_border_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getHorizontalBoxShadow($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_layerednavigation/sidebar/horizontal_box_shadow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

}

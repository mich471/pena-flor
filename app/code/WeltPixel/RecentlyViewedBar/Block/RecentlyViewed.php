<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_RecentlyViewedBar
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */

namespace WeltPixel\RecentlyViewedBar\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use WeltPixel\RecentlyViewedBar\Helper\Data as WpHelper;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class RecentlyViewed
 * @package WeltPixel\ShopByBrand\Block
 */
class RecentlyViewed extends \Magento\Framework\View\Element\Template
{

    const CMS_PAGE_ROUTE = 'cms';
    const CATALOG_PAGE_ROUTE = 'catalog';
    const CATEGORY_PAGE_CONTROLLER = 'category';
    const PRODUCT_PAGE_CONTROLLER = 'product';
    const ADD_TO_CART_BUTTON_CONFIG_VAL  = '1';


    /**
     * @var array
     */
    protected $_showAttributes = [
        '1' => 'image',
        '2' => 'name',
        '3' => 'price'
    ];
    /**
     * @var array
     */
    protected $_showButtons = [
        '1' => 'add_to_cart',
        '2' => 'add_to_compare',
        '3' => 'add_to_wishlist',
    ];
    /**
     * @var
     */
    protected $_wpHelper;
    /**
     * @var array
     */
    public $configData = [];
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var
     */
    protected $itemHeight;
    /**
     * @var ProductMetadataInterface
     */
    protected $metadataInterface;

    /**
     * RecentlyViewed constructor.
     * @param Template\Context $context
     * @param WpHelper $wpHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        WpHelper $wpHelper,
        Http $request,
        ProductMetadataInterface $metadataInterface,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_wpHelper = $wpHelper;
        $this->request = $request;
        $this->metadataInterface = $metadataInterface;
    }

    /**
     * @return array
     */
    public function getConfigData()
    {
        if(!empty($this->configData)){
            return $this->configData;
        }

        $isEnabled = $this->_wpHelper->isEnabled();
        $displayOn = $this->_wpHelper->getDisplayOn();
        $showAttributes = $this->getShowAttributes();
        $showButtons = $this->getShowButtons();
        $enableCms = $this->_wpHelper->getEnableCms();
        $enableSl = $this->_wpHelper->getEnableSlIntegration();
        $cmsBlock = $this->_wpHelper->getCmsBlock();
        $buttonColor = $this->_wpHelper->getButtonColor();
        $buttonTextColor = $this->_wpHelper->getButtonTextColor();
        $buttonLabel = $this->_wpHelper->getButtonLabel();
        $itemLimit = $this->_wpHelper->getItemLimit();
        $isAddToCartEnabled = $this->isAddToCartEnabled();
        $isJustAddToCart = $this->isOnlyAddtoCartEnabled();
        $visibility = $this->getVisibility();

        $this->configData = [
            'is_enabled' => $isEnabled,
            'display_on' => $displayOn,
            'show_attributes' => $showAttributes,
            'show_buttons' => $showButtons,
            'enable_cms' => $enableCms,
            'enable_sl' => $enableSl,
            'cms_block' => $cmsBlock,
            'button_color' => $buttonColor,
            'button_text_color' => $buttonTextColor,
            'button_label' => $buttonLabel,
            'item_limit' => $itemLimit,
            'is_add_to_cart_enabled' => $isAddToCartEnabled,
            'is_just_add_to_cart' => $isJustAddToCart,
            'is_visible' => $visibility
        ];

        return $this->configData;

    }

    /**
     * @return bool
     */
    protected function isAddToCartEnabled(){
        $buttonsArr = explode(',', $this->_wpHelper->getShowButtons());
        $isAddToCart = false;
        if(in_array(self::ADD_TO_CART_BUTTON_CONFIG_VAL,$buttonsArr)){
            $isAddToCart = true;
        }
        return $isAddToCart;
    }

    /**
     * @return bool
     */
    protected function isOnlyAddtoCartEnabled(){
        $buttonsArr = explode(',', $this->_wpHelper->getShowButtons());
        $isOnly = false;
        if(count($buttonsArr) == 1 && in_array(self::ADD_TO_CART_BUTTON_CONFIG_VAL,$buttonsArr)){
            $isOnly = true;
        }
        return $isOnly;
    }

    /**
     * @return array
     */
    protected function getShowButtons()
    {
        $buttonsArr = explode(',', $this->_wpHelper->getShowButtons());
        $buttonsStr = '';
        foreach($buttonsArr as $btn) {
            if(array_key_exists($btn, $this->_showButtons)){
                $buttonsStr .= $this->_showButtons[$btn] . ',';
            }
        }
        if($buttonsStr) {
            rtrim($buttonsStr, ',');
        }

        return $buttonsStr;
    }

    /**
     * @return array
     */
    protected function getShowAttributes()
    {
        $attributesArr = explode(',', $this->_wpHelper->getShowAttributes());
        $attributesStr = '';
        foreach($attributesArr as $attr) {
            if(array_key_exists($attr, $this->_showAttributes)){
                $attributesStr .= $this->_showAttributes[$attr] . ',';
            }
        }

        if($attributesStr) {
            rtrim($attributesStr, ',');
        }

        return $attributesStr;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->_request->getRouteName();
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->_request->getControllerName();
    }

    /**
     * @return $this
     */
    protected function getVisibility()
    {
        $route = $this->getRoute();
        $controller = $this->getController();
        $displayOn = $this->_wpHelper->getDisplayOn();
        $displOnArray = explode(',', $displayOn);
        $isVisible = false;

        if ($this->request->isAjax()) {
            $isVisible = false;
        } else {
            if (in_array(1, $displOnArray) && ($route == self::CMS_PAGE_ROUTE || $route == self::CATALOG_PAGE_ROUTE) ||
                in_array(2, $displOnArray) && $route == self::CATALOG_PAGE_ROUTE && $controller == self::CATEGORY_PAGE_CONTROLLER ||
                in_array(3, $displOnArray) && $route == self::CATALOG_PAGE_ROUTE && $controller == self::PRODUCT_PAGE_CONTROLLER ||
                in_array(4, $displOnArray) && $route == self::CMS_PAGE_ROUTE
            ) {
                $isVisible = true;
            }

        }

        return $isVisible;

    }

    /**
     * @return int
     */
    public function getItemHeight(){
        if($this->itemHeight){
            return $this->itemHeight;
        }
        $this->itemHeight = 20;
        if($this->configData['show_buttons']){
            $buttonsArray = explode(',', $this->configData['show_buttons']);
            if(in_array('add_to_cart', $buttonsArray)){
                $this->itemHeight += 35;
            }
            if(in_array('add_to_compare', $buttonsArray)){
                $this->itemHeight += 20;
            }
            if(in_array('add_to_wishlist', $buttonsArray)){
                $this->itemHeight += 20;
            }
        }
        if($this->configData['show_attributes']){
            $attributesArray = explode(',', $this->configData['show_attributes']);
            if(in_array('image', $attributesArray)){
                $this->itemHeight += 224;
            }
            if(in_array('price', $attributesArray)){
                $this->itemHeight += 24;
            }
            if(in_array('name', $attributesArray)){
                $this->itemHeight += 40;
            }
        }
        return $this->itemHeight;
    }

    /**
     * @return bool
     */
    public function isUICompatible() {
        $vString = $this->metadataInterface->getVersion();
        $vArr = explode('.', $vString);
        if(isset($vArr[1])) {
            if($vArr[1] >= 2){
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function loadRecentlyViewedWidget()
    {
        return $this->getLayout()->createBlock(
            "Magento\Catalog\Block\Widget\RecentlyViewed",
            "recently_viewed",
            [
                "data" => [
                    "uiComponent" => "widget_recently_viewed",
                    "page_size"   => $this->configData['item_limit'],
                    "show_attributes" => $this->configData['show_attributes'],
                    "show_buttons" => $this->configData['show_buttons']
                ]
            ]
        )->setTemplate("Magento_Catalog::product/widget/viewed/grid.phtml");
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function loadCmsBlock()
    {
        if ($this->configData['enable_sl'] && !$this->_wpHelper->isCustomerLoggedIn()) {
            return $this->getSocialLoginWidget();
        }

        return $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($this->configData['cms_block']);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    protected function getSocialLoginWidget()
    {
        $slWidget = $this->getLayout()
            ->createBlock('WeltPixel\SocialLogin\Block\Widget\Login')
            ->setTemplate('widget/login.phtml');

        return $slWidget;
    }
}
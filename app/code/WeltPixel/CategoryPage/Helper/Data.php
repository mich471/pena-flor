<?php

namespace WeltPixel\CategoryPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displayReviews($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/review/display_reviews', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displayAddToWishlist($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/display_wishlist', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displayAddToCompare($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/display_compare', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displaySwatches($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/display_swatches', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displaySwatchTooltip($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/display_swatch_tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDefaultLineHeight($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default/line____height__computed', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isHoverImageEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/image/enable_hover_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displayAddToCart($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/display_addtocart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function alignAddToCart($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/addtocart_align', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductsPerLine($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/products_per_line', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getLayeredNavigationSwatchOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/swatch_layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingSwatchOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/swatch_productlisting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingItemOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/item', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCategoryDescriptionOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingNameOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingPriceOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getProductListingReviewOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/review', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getToolbarOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/toolbar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return string
     */
    public function getRelatedProductListItemsTemplate()
    {
        $template = 'Magento_Catalog::product/list/items.phtml';
        if ($this->scopeConfig->getValue('weltpixel_productlabels/general/enable_related_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_ProductLabels::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_ga4/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_GA4::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_googletagmanager/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_GoogleTagManager::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_owl_carousel_config/related_products/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_OwlCarouselSlider::product/list/items.phtml';
        }

        return $template;
    }

    /**
     * @return string
     */
    public function getCrossellProductListItemsTemplate()
    {
        $template = 'Magento_Catalog::product/list/items.phtml';
        if ($this->scopeConfig->getValue('weltpixel_productlabels/general/enable_crossell_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_ProductLabels::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_ga4/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_GA4::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_googletagmanager/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_GoogleTagManager::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_owl_carousel_config/crosssell_products/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_OwlCarouselSlider::product/list/items.phtml';
        }
        return $template;
    }

    /**
     * @return string
     */
    public function getUpsellProductListItemsTemplate()
    {
        $template = 'Magento_Catalog::product/list/items.phtml';
        if ($this->scopeConfig->getValue('weltpixel_productlabels/general/enable_upsell_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_ProductLabels::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_ga4/general/product_click_tracking', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $template = 'WeltPixel_GA4::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_googletagmanager/general/product_click_tracking')) {
            $template = 'WeltPixel_GoogleTagManager::product/list/items.phtml';
        }
        if ($this->scopeConfig->getValue('weltpixel_owl_carousel_config/upsell_products/status')) {
            $template = 'WeltPixel_OwlCarouselSlider::product/list/items.phtml';
        }
        return $template;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getLayeredNavigationBulletOptions($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/layered_navigation_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCategoryPageProductsHoverAnimation($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/hover_products_animation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCategoryPageProductsHoverAnimationSpeed($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_category_page/general/hover_products_animation_speed', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isLazyLoadingEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_lazy_loading/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isOwlCarouselHoverImageEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_owl_slider_config/general/enable_hover_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}

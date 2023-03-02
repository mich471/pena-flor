<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Block;

use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Helper\Product\ProductList;

/**
 * Class Navigation
 * @package WeltPixel\LayeredNavigation\Block
 */
class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Catalog\Model\Layer\AvailabilityFlagInterface
     */
    protected $visibilityFlag;

    /**
     * @var ProductList
     */
    protected $_productListHelper;

    /**
     * Default Order field
     *
     * @var string
     */
    protected $_orderField = null;

    /**
     * Default direction
     *
     * @var string
     */
    protected $_direction = ProductList::DEFAULT_SORT_DIRECTION;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \WeltPixel\LayeredNavigation\Model\AttributeOptions
     */
    protected $_attributeOptions;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;

    /**
     * Navigation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param ProductList $productListHelper
     * @param \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
     * @param \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        ProductList $productListHelper,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions,
        \Magento\Framework\Registry $registry,
        \Magento\Swatches\Helper\Data $swatchHelper,
        array $data = []
    )
    {
        $this->_catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->visibilityFlag = $visibilityFlag;
        $this->_productListHelper = $productListHelper;
        $this->_wpHelper = $wpHelper;
        $this->_attributeOptions = $attributeOptions;
        $this->_registry = $registry;
        $this->swatchHelper = $swatchHelper;
        parent::__construct($context, $layerResolver, $filterList, $visibilityFlag, $data);
    }
    /**
     * @return mixed
     */
    public function isCategoryFilterVisible()
    {
        return $this->_wpHelper->showCategoriesBlock();
    }

    /**
     * @return mixed
     */
    public function isAjaxMode()
    {
        return $this->_wpHelper->isAjaxEnabled();
    }

    /**
     * @return mixed
     */
    public function isAutoClose()
    {
        return $this->_wpHelper->isAutoClose();
    }

    /**
     * @return mixed
     */
    public function noOfColumns() {
        return $this->_wpHelper->getNoColumns();
    }

    /**
     * @return mixed
     */
    public function boxHeight() {
        return $this->_wpHelper->getSlideDownHeight();
    }
    /**
     * @return mixed
     */
    public function filterColumnHeight() {
        return $this->_wpHelper->getFilterColumnHeight();
    }

    /**
     * check if current filter is a category filter
     *
     * @param $filter
     * @return bool
     */
    public function isCategoryFilter($filter)
    {
        return ($filter->getRequestVar() == $this->_wpHelper->getCategoryParamLabel()) ? true : false;
    }

    /**
     * check if current filter is a rating filter
     *
     * @param $filter
     * @return bool
     */
    public function isRatingFilter($filter)
    {
        return ($filter->getRequestVar() == $this->_wpHelper->getRatingParamLabel()) ? true : false;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getFilterButtonHtml()
    {
        return $this->_wpHelper->getFilterButtonStyle();
    }

    /**
     * Return wp attribute options
     *
     * @param $attributeId
     * @return mixed
     */
    public function getWpAttributeOptions($attributeId)
    {
        return $this->_attributeOptions->getDisplayOptionsByAttribute($attributeId);
    }

    /**
     * check if module enabled and ajax mode is enabled
     *
     * @return bool
     */
    public function isLnEnabled() {
        $is = false;
        if($this->_wpHelper->isEnabled()) {
            $is = ($this->_wpHelper->isAjaxEnabled()) ? true : false;
        }

        return $is;
    }

    /**
     * set filter tab status
     *
     * @return string
     */
    public function getActiveFilters() {
        $filters = $this->getFilters();
        $activeFilters = [];
        $ctr = 0;
        $isSlideDownMode = ($this->_wpHelper->getSidebarStyle() == 2 ) ? true : false;
        foreach($filters as $k => $filter) {

            if($filter->getRequestVar() == $this->_wpHelper->getCategoryParamLabel() && !$isSlideDownMode) {
                if($filter->getItemsCount() > 0) $ctr++;
                continue;
            } else {

                if($filter->getItemsCount() > 0) {

                    if($isSlideDownMode) {
                        $activeFilters[] = $ctr;
                        $ctr++;
                        continue;
                    }

                    if($filter->getRequestVar() == $this->_wpHelper->getRatingParamLabel()) {
                        if($this->_wpHelper->getRatingFilterDisplay() == '1') $activeFilters[] = $ctr;
                        if($filter->getItemsCount()) $ctr++;
                        continue;
                    }
                    $attributeId = $filter->getAttributeModel()->getAttributeId();
                    $categoryVisibility = $this->getCategoryVisibility($attributeId);
                    if(!$categoryVisibility) {
                        continue;
                    }

                    if($attributeId) {
                        $wpOptions = $this->getWpAttributeOptions($attributeId);
                        if($wpOptions->getData()) {

                            if($wpOptions->getDisplayOption() == '1' || $wpOptions->getDisplayOption() == '2') {
                                $activeFilters[] = $ctr;
                            }
                        }

                        if ($this->swatchHelper->isSwatchAttribute($filter->getAttributeModel())) {
                            if($wpOptions->getDisplayOption() == '1') {
                                $activeFilters[] = $ctr;
                            }
                        }
                    }
                    $ctr++;
                }
            }

        }

        $activeFiltersStr = implode(' ', $activeFilters);

        return $activeFiltersStr;
    }


    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());
        $options = [
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => $this->_direction ?: ProductList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->getOrderField(),
            'limitDefault' => $this->_productListHelper->getDefaultLimitPerPageValue($defaultMode),
            'url' => $this->getPagerUrl(),
        ];
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(['productListToolbarForm' => $options]);
    }

    /**
     * Get order field
     *
     * @return null|string
     */
    protected function getOrderField()
    {
        if ($this->_orderField === null) {
            $this->_orderField = $this->_productListHelper->getDefaultSortField();
        }
        return $this->_orderField;
    }

    /**
     * @return bool
     */
    public function getCategoryVisibility($attributeId = false)
    {
        if(!$attributeId) {
            return true;
        }
        $visibility = true;
        $category = $this->_registry->registry('current_category');
        if ($attributeId > 0 && $category) {
            $currentCatId = $category->getId();
            $categoryVisibility = $this->getWpAttributeOptions($attributeId)->getCategoryVisibility();
            $categoriesIds = $this->getWpAttributeOptions($attributeId)->getCategoryIds();
            $categoriesArr = explode(',', $categoriesIds);

            if ($categoryVisibility == 1) {
                if (in_array($currentCatId, $categoriesArr)) {
                    $visibility = true;
                } else {
                    $visibility = false;
                }
            }

            if ($categoryVisibility == 2) {
                if (in_array($currentCatId, $categoriesArr)) {
                    $visibility = false;
                } else {
                    $visibility = true;
                }
            }
        }

        return $visibility;
    }
}
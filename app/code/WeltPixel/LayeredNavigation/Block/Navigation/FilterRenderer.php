<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Block\Navigation;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

/**
 * Class FilterRenderer
 * @package WeltPixel\LayeredNavigation\Block\Navigation
 */
class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @var \WeltPixel\LayeredNavigation\Model\AttributeOptions
     */
    protected $_attributeOptions;
    /**
     * @var
     */
    protected $_attributeId;
    /**
     * @var
     */
    protected $_attributeOptionsObj;
    /**
     * @var int
     */
    protected $maxPrice;
    /**
     * @var int
     */
    protected $minPrice;
    /**
     * @var int
     */
    protected $maxPriceX;
    /**
     * @var int
     */
    protected $minPriceX;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_currency;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    private $htmlPagerBlock;

    /**
     * FilterRenderer constructor.
     * @param \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
     * @param \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $currency
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        \WeltPixel\LayeredNavigation\Model\AttributeOptions $attributeOptions,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $currency,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_wpHelper = $wpHelper;
        $this->_attributeOptions = $attributeOptions;
        $this->_registry = $registry;
        $this->_currency = $currency;
        $this->htmlPagerBlock = $htmlPagerBlock;
        parent::__construct($context, $data);
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        $this->filter = $filter;

        if ($this->_wpHelper->isEnabled()) {
            if ($filter->getRequestVar() == 'price' && $this->_wpHelper->getPriceIsSliderMode()) {
                $collection = $this->filter->getLayer()->getProductCollection();
                $currentCatId = ($this->_registry->registry('current_category')) ? $this->_registry->registry('current_category')->getId() : "";
                $basePriceData = $this->_registry->registry('price_filter');

                if (is_array($basePriceData) && $currentCatId && array_key_exists($currentCatId, $basePriceData)) {
                    $this->minPriceX = $basePriceData[$currentCatId]['min'];
                    $this->maxPriceX = $basePriceData[$currentCatId]['max'];
                } else {
                    $this->minPriceX = ($this->minPriceX == null) ? $collection->getMinPrice() : $this->minPriceX;
                    $this->maxPriceX = ($this->maxPriceX == null) ? $collection->getMaxPrice() : $this->maxPriceX;
                }
                $this->minPrice = ($this->minPrice == null) ? $collection->getMinPrice() : $this->minPrice;
                $this->maxPrice = ($this->maxPrice == null) ? $collection->getMaxPrice() : $this->maxPrice;
                $this->setTemplate('layer/slider.phtml');
            } else {
                $this->setTemplate('layer/filter.phtml');
            }
        }

        $html = parent::render($filter);
        return $html;
    }

    /**
     * @return array
     */
    public function getPriceConfigData()
    {

        $range = [
            'minX' => floor($this->minPriceX),
            'maxX' => ceil($this->maxPriceX),
            'min' => floor($this->minPrice),
            'max' => ceil($this->maxPrice),
            'step' => 1,
            'currency' => $this->_currency->getCurrencySymbol()
        ];

        return $range;
    }

    /**
     * @return bool
     */
    public function priceIsInputType()
    {
        return $this->_wpHelper->canShowPriceInput();
    }


    /**
     * @return string
     */
    public function getSliderApplyUrl()
    {
        $params = [
            'price' => $this->minPrice . '-' . $this->maxPrice,
            $this->htmlPagerBlock->getPageVarName() => null
        ];
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * @return mixed
     */
    public function getPriceStep()
    {
        $step = $this->_wpHelper->getPriceRangeStep();
        return ($step > $this->maxPrice) ? $this->maxPrice : $step;
    }

    /**
     * @param $filter
     */
    public function setAttributeId($filter)
    {
        if ($filter->getRequestVar() == $this->_wpHelper->getCategoryParamLabel()) {
            $this->_attributeId = $this->_wpHelper->getCategoryParamLabel();
        } elseif ($filter->getRequestVar() == $this->_wpHelper->getRatingParamLabel()) {
            $this->_attributeId = $this->_wpHelper->getRatingParamLabel();
        } else {
            $this->_attributeId = $filter->getAttributeModel()->getAttributeId();
        }
    }

    /**
     * Return wp attribute options
     *
     * @param $attributeId
     * @return mixed
     */
    public function getWpAttributeOptions()
    {
        $this->_attributeOptionsObj = ($this->_attributeId > 0) ? $this->_attributeOptions->getDisplayOptionsByAttribute($this->_attributeId) : '';

        return $this->_attributeOptionsObj;
    }

    /**
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->_attributeId;
    }

    /**
     * return the 'Visible Options' attribute configuration value
     *
     * @return mixed
     */
    public function getVisibleItems()
    {
        return ($this->_attributeId > 0) ? $this->getWpAttributeOptions($this->_attributeId)->getVisibleOptions() : '';
    }

    /**
     * return the 'Visible Options Step' attribute configuration value
     *
     * @return mixed
     */
    public function getVisibleItemsStep()
    {
        return ($this->_attributeId > 0) ? $this->getWpAttributeOptions($this->_attributeId)->getVisibleOptionsStep() : '';
    }

    /**
     * return the 'Show Qty' attribute configuration value
     *
     * @return mixed
     */
    public function getShowQty()
    {
        if ($this->_attributeId == $this->_wpHelper->getRatingParamLabel()) {
            return $this->_wpHelper->getRatingFilterCounter();
        } elseif ($this->_attributeId == $this->_wpHelper->getCategoryParamLabel()) {
            return '';
        } else {
            return $this->getWpAttributeOptions($this->_attributeId)->getShowQuantity();
        }
    }

    /**
     * return the 'Is Multiselect' attribute configuration value
     *
     * @return mixed
     */
    public function getIsMultiSelect()
    {
        if ($this->_attributeId == $this->_wpHelper->getRatingParamLabel()) {
            return $this->_wpHelper->isRatingFilterMultiselect();
        } elseif ($this->_attributeId == $this->_wpHelper->getCategoryParamLabel()) {
            return '';
        } else {
            return $this->getWpAttributeOptions($this->_attributeId)->getIsMultiselect();
        }
    }

    /**
     * return the 'Instant Search' attribute configuration value
     *
     * @return string
     */
    public function canShowInstantSearch()
    {
        return ($this->_attributeId > 0) ? $this->getWpAttributeOptions($this->_attributeId)->getInstantSearch() : '';
    }

    /**
     * return the 'Instant Search Mobile' attribute configuration value
     *
     * @return string
     */
    public function canShowInstantSearchMobile()
    {
        return ($this->_attributeId > 0) ? $this->getWpAttributeOptions($this->_attributeId)->getInstantSearchMobile() : '';
    }

    /**
     * @return string
     */
    public function getCategoryParamLabel()
    {
        return $this->_wpHelper->getCategoryParamLabel();
    }

    /**
     * @return string
     */
    public function getRatingParamLabel()
    {
        return $this->_wpHelper->getRatingParamLabel();
    }


}

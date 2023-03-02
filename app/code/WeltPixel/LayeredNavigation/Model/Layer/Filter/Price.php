<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Price as AbstractFilter;
use WeltPixel\LayeredNavigation\Helper\Data as LayerHelper;
use WeltPixel\LayeredNavigation\Model\AttributeOptions;

/**
 * Class Price
 * @package WeltPixel\LayeredNavigation\Model\Layer\Filter
 */
class Price extends AbstractFilter
{
    /** @var \WeltPixel\LayeredNavigation\Helper\Data */
    protected $_moduleHelper;

    /** @var array|null Filter value */
    protected $_filterVal = null;

    /** @var \Magento\Tax\Helper\Data */
    protected $_taxHelper;

    /** @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price */
    private $dataProvider;

    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    private $priceCurrency;

    /** @var \Magento\Framework\Registry */
    protected $_registry;

    protected $_scopeConfig;

    /**
     * @var AttributeOptions
     */
    protected $_wpAttributeOptions;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * Price constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param LayerHelper $moduleHelper
     * @param AttributeOptions $attributeOptions
     * @param \WeltPixel\LayeredNavigation\Helper\Data $wpHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Tax\Helper\Data $taxHelper,
        LayerHelper $moduleHelper,
        AttributeOptions $attributeOptions,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );

        $this->priceCurrency = $priceCurrency;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->_moduleHelper = $moduleHelper;
        $this->_taxHelper = $taxHelper;
        $this->_registry = $registry;
        $this->_scopeConfig = $scopeConfig;
        $this->_wpAttributeOptions = $attributeOptions;
        $this->_wpHelper = $wpHelper;
    }

    /**
     * @inheritdoc
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $priceRangeCalculation = $this->_scopeConfig->getValue(
            \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if($priceRangeCalculation != 'auto') {
            return parent::apply($request);
        }


        $filterEntry = [];
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::apply($request);
        }
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $strFilter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter)) {
            return $this;
        }
        $collection = $this->getLayer()->getProductCollection();
        $baseCollection = clone $collection;

        $currentCatId = $this->_registry->registry('current_category') ? $this->_registry->registry('current_category')->getId() : false;
        $priceFilter = $this->_registry->registry('price_filter') ?: [];

        if ($currentCatId) {
            if (!array_key_exists($currentCatId, $priceFilter)) {
                $basePriceData = [
                    'min' => $baseCollection->getMinPrice(),
                    'max' => $baseCollection->getMaxPrice()
                ];
                $filterEntry = [
                    $currentCatId => $basePriceData
                ];
                $this->_registry->register('price_filter', $filterEntry);
            }
        }


        $filterParams = explode(',', $filter);

        $attribute = $this->getAttributeModel();
        $wpLnAttributeOptions = ($attribute->getId()) ? $this->_wpAttributeOptions->getDisplayOptionsByAttribute($attribute->getId()) : false;
        if (!$this->_wpHelper->getPriceIsSliderMode() && $wpLnAttributeOptions->getIsMultiselect()) {
            $filterConditions = [];

            foreach ($filterParams as $filterParam) {

                $filter = $this->dataProvider->validateFilter($filterParam);
                if (!$filter) {
                    continue;
                }
                $this->dataProvider->setInterval($filter);
                $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
                if ($priorFilters) {
                    $this->dataProvider->setPriorIntervals($filterParams);
                }

                list($from, $to) = $this->_filterVal = $filter;
                $filterConditions[] = [
                    'attribute' => 'price',
                    ['from' => $from, 'to' => $to]
                ];

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
                );
            }
            $this->getLayer()->getProductCollection()->addAttributeToFilter(
                $filterConditions
            );
        } else {
            $filter = $this->dataProvider->validateFilter($filterParams[0]);
            if (!$filter) {
                return $this;
            }

            $this->dataProvider->setInterval($filter);
            $priorFilters = $this->dataProvider->getPriorFilters($filterParams);
            if ($priorFilters) {
                $this->dataProvider->setPriorIntervals($priorFilters);
            }

            list($from, $to) = $this->_filterVal = $filter;

            $this->getLayer()->getProductCollection()->addFieldToFilter(
                'price',
                ['from' => $from, 'to' => $to]
            );

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
            );

        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _renderRangeLabel($fromPrice, $toPrice, $isLast = false)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_renderRangeLabel($fromPrice, $toPrice, $isLast);
        }
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if ($toPrice === '' || $isLast) {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getItemsData()
    {
        /*
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }
*/
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        $wpLnAttributeOptions = ($attribute->getId()) ? $this->_wpAttributeOptions->getDisplayOptionsByAttribute($attribute->getId()) : false;

        if (!$this->_wpHelper->getPriceIsSliderMode() && !$wpLnAttributeOptions->getIsMultiselect() && $this->_filterVal) {
            return [];
        }

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();

        if ($this->_filterVal) {
            /** @type \WeltPixel\LayeredNavigation\Model\ResourceModel\Fulltext\Collection $productCollectionClone */
            $productCollection = $productCollection->getCollectionClone()
                ->removeAttributeSearch(['price.from', 'price.to']);
        }

        $facets = $productCollection->getFacetedData($attribute->getAttributeCode());

        $data = [];
        if (count($facets) > 1) {
            $lastFacet = array_key_last($facets);
            foreach ($facets as $key => $aggregation) {
                $count = $aggregation['count'];
                if (strpos($key, '_') === false) {
                    continue;
                }
                $isLast = $lastFacet === $key;
                $data[] = $this->prepareData($key, $count, $wpLnAttributeOptions->getIsMultiselect(), $isLast);
            }
        }

        return $data;
    }

    /**
     * @param string $key
     * @param int $count
     * @param bool $isMultiSelect
     * @param boolean $isLast
     * @return array
     */
    private function prepareData($key, $count, $isMultiSelect, $isLast = false)
    {
        list($from, $to) = explode('_', $key);
        if ($from == '*') {
            $from = $this->getFrom($to);
        }
        if ($to == '*') {
            $to = $this->getTo($to);
        }
        $label = $this->_renderRangeLabel(
            empty($from) ? 0 : $from * $this->getCurrencyRate(),
            empty($to) ? $to : $to * $this->getCurrencyRate(),
            $isLast
        );

        $value = $from . '-' . $to;
        if (!$isMultiSelect) {
            $value .= $this->dataProvider->getAdditionalRequestData();
        }

        $data = [
            'label' => $label,
            'value' => $value,
            'count' => $count,
            'from' => $from,
            'to' => $to,
        ];

        return $data;
    }
}



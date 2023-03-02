<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Plugin\Layer;

use  WeltPixel\LayeredNavigation\Helper\Data;

/**
 * Class FilterList
 * @package WeltPixel\LayeredNavigation\Plugin\Layer
 */
class FilterList
{
    const RATING_FILTER_CLASS = 'WeltPixel\LayeredNavigation\Model\Layer\Filter\Rating';
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_layer;

    /**
     * FilterList constructor.
     * @param Data $wpHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        Data $wpHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_wpHelper = $wpHelper;
        $this->_objectManager = $objectManager;
    }

    /**
     * Remove category filter if disabled in configuration
     *
     * @param \Magento\Catalog\Model\Layer\FilterList $subject
     * @param $result
     * @return array
     */
    public function afterGetFilters(\Magento\Catalog\Model\Layer\FilterList $subject, $result)
    {
        if(!$this->_wpHelper->isEnabled()){
            return $result;
        }
        $filteredResult = [];
        if(!$this->_wpHelper->showCategoriesBlock()) {
            foreach($result as $r) {
                if($r->getRequestVar() != $this->_wpHelper->getCategoryParamLabel()) {
                    $filteredResult[] = $r;
                }
            }
            return $filteredResult;

        } else {
            return $result;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|mixed
     */

    public function aroundGetFilters( \Magento\Catalog\Model\Layer\FilterList $subject, \Closure $proceed, \Magento\Catalog\Model\Layer $layer)
    {
        if(!$this->_wpHelper->isEnabled()){
            $result = $proceed($layer);
            return $result;
        }
        $result = $proceed($layer);
        if($this->_wpHelper->getRatingFilterName()) {
            $result[] = $this->getRatingFilter($layer);
            $result = $this->_injectRatingFilter($result);

        }

        return $result;

    }

    /**
     * @param $filters
     * @return mixed
     */
    protected function _injectRatingFilter($filters) {

        $ratingFilterPosition = $this->_wpHelper->getRatingFilterPosition();
        if(count($filters) > $ratingFilterPosition) {
            foreach($filters as $k => $filter) {
                $class = self::RATING_FILTER_CLASS;
                if($filter instanceof $class){
                    $ratingFilterKey = $k;
                    break;
                }
            }


            $ratingFilterArr = array_splice($filters, $ratingFilterKey, 1);
            array_splice($filters, $ratingFilterPosition, 0, $ratingFilterArr);
        }

        return $filters;

    }

    /**
     * @param $layer
     * @return mixed
     */
    public function getRatingFilter($layer)
    {
        $filter = $this->_objectManager->create(
            $this->getRatingFilterClass(),
            ['layer' => $layer]
        );
        return $filter;
    }

    /**
     * @return string
     */
    public function getRatingFilterClass() {

        return self::RATING_FILTER_CLASS;

    }
}

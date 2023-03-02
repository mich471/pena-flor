<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\Layer\Filter;


use Magento\Catalog\Model\Layer;
use WeltPixel\LayeredNavigation\Helper\Data as LayerHelper;

/**
 * Layer category filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rating extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    /**
     * Active Category Id
     *
     * @var int
     */
    protected $_categoryId;

    /**
     * Applied Category
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_appliedCategory;

    /**
     * Core data
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var CategoryDataProvider
     */
    private $dataProvider;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * Rating constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Layer $layer
     * @param Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param LayerHelper $moduleHelper
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        LayerHelper $moduleHelper,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_escaper = $escaper;
        $this->_wpHelper = $moduleHelper;
        $this->_requestVar = $this->_wpHelper->getRatingParamLabel();
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Apply category filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filters = explode(',',$filter);

        $collection = $this->getLayer()->getProductCollection();
        $noRatingCollection = $collection->getCollectionClone();
        $this->_coreRegistry->register('no_rating_coll', $noRatingCollection);
        $collection->getSelect()->joinLeft(array('rating'=> 'rating_option_vote_aggregated'),'e.entity_id =rating.entity_pk_value',array("percent"))
                                ->where("rating.store_id = ?", $this->_storeManager->getStore()->getId())
                                ->group('e.entity_id');
        foreach($filters as  $reqFilter){
            list($from,$to) = explode('-', $reqFilter);
            $conditionArr[] = new \Zend_Db_Expr("rating.percent BETWEEN ".$from." AND ".$to);

        }
        $condString = '';
        foreach($conditionArr as $condition) {
            $condString .= " {$condition} OR";
        }

        $collection->getSelect()->where(substr($condString, 0, -2));

        foreach($filters as  $reqFilter) {

            $state = $this->_createItem($this->getLabelHtml($reqFilter), $reqFilter)
                ->setVar($this->_requestVar);
            $this->getLayer()->getState()->addFilter($state);
        }
        $collection->getSize();
        return $this;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        $name = ($this->_wpHelper->getRatingFilterName()) ? $this->_wpHelper->getRatingFilterName() : 'Rating';
        return __($name);
    }


    /**
     * Get data array for building attribute filter items
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getItemsData()
    {
        $collection = $this->_coreRegistry->registry('no_rating_coll');
        if (!$this->_wpHelper->isRatingFilterMultiselect() && isset($collection) ) {
            return [];
        }
        $facets = array(
            '0-20' => $this->_getRatingLabelHtml(1),
            '21-40' => $this->_getRatingLabelHtml(2),
            '41-60' => $this->_getRatingLabelHtml(3),
            '61-80' => $this->_getRatingLabelHtml(4),
            '81-100' => $this->_getRatingLabelHtml(5),
        );
        if (count($facets) > 1) { // two range minimum
            $i=1;

            foreach ($facets as $key => $label) {
                if (!$collection) {
                    $collection = $this->getLayer()->getProductCollection();
                }
                $clonedCollection = clone $collection;
                $count = $this->prepareData($key,$clonedCollection,$i);
                $i++;
                if($count > 0) {
                    $this->itemDataBuilder->addItemData(
                        $label,
                        $key,
                        $count
                    );
                }
            }
        }

        return $this->itemDataBuilder->build();

    }

    /**
     * @param string $key
     * @param int $count
     * @return array
     */
    private function prepareData($filter,$collection,$i)
    {
        $filter = explode('-', $filter);
        list($from, $to) = $filter;
        $collection->clear();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $collection->getSelect()->joinLeft(array('rating'.$i=> 'rating_option_vote_aggregated'),'e.entity_id =rating'.$i.'.entity_pk_value',array("percent"))
            ->where("rating".$i.".percent between ".$from." and ".$to)
            ->where("rating".$i.".store_id = ?", $this->_storeManager->getStore()->getId())
            ->group('e.entity_id');
        $collection->load();
        return $collection->count();


    }

    /**
     * @param $countStars
     * @return string
     */
    protected function _getRatingLabelHtml($countStars)
    {
        $html = '<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                    <div class="rating-result" title="up to '.$countStars*20 .'%"">
                        <span style="width:'.$countStars*20 .'%"></span>
                    </div>
                </div>';
        return $html;
    }

}

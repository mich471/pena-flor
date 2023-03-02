<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Observer\Edit\Tab\Front;

use Magento\Config\Model\Config\Source;
use Magento\Framework\Module\Manager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use WeltPixel\LayeredNavigation\Model\AttributeOptions;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Helper\Category;

/**
 * Class WpProductAttributeFormBuildFrontTabObserver
 * @package WeltPixel\LayeredNavigation\Observer\Edit\Tab\Front
 */
class WpProductAttributeFormBuildFrontTabObserver implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $optionList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var AttributeOptions
     */
    protected $_wpModel;

    protected $_wpAttributeObj = false;
    /**
     * @var Attribute
     */
    protected $_attributeModel;

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tree
     */
    protected $adminCategoryTree;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;

    /**
     * WpProductAttributeFormBuildFrontTabObserver constructor.
     * @param Manager $moduleManager
     * @param Source\Yesno $optionList
     * @param Http $request
     * @param AttributeOptions $wpModel
     * @param Attribute $attributeModel
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Block\Adminhtml\Category\Tree $adminCategoryTree
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Manager $moduleManager,
        Source\Yesno $optionList,
        Http $request,
        AttributeOptions $wpModel,
        Attribute $attributeModel,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Block\Adminhtml\Category\Tree $adminCategoryTree
    )
    {
        $this->optionList = $optionList;
        $this->moduleManager = $moduleManager;
        $this->_request = $request;
        $this->_wpModel = $wpModel;
        $this->_attributeModel = $attributeModel;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->adminCategoryTree = $adminCategoryTree;

        $this->_getWpAttributeOptionValues();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('WeltPixel_LayeredNavigation')) {
            return;
        }
        $isSwatchAttr = $this->_isSwatchAttr();
        $swatchComment = $isSwatchAttr ?  '<br/>This setting can not be used with swatch attribute types.' : '';

        /** @var \Magento\Framework\Data\Form\AbstractForm $form */
        $form = $observer->getForm();

        $fieldset = $form->addFieldset(
            'wp_front_fieldset',
            ['legend' => __('WeltPixel Layered Navigation Properties'), 'collapsable' => false]
        );
        $field = '';

        if(!$isSwatchAttr) {
            $value = [
                ['value' => 0, 'label' => __('Closed')],
                ['value' => 1, 'label' => __('Fully Opened')],
                ['value' => 2, 'label' => __('Expandable')],
            ];
        } else {
            $value = [
                ['value' => 0, 'label' => __('Closed')],
                ['value' => 1, 'label' => __('Fully Opened')],
            ];
        }

        $fieldset->addField(
            'wp_display_options',
            'select',
            [
                'name' => 'wp_display_options',
                'label' => __("Show Attribute Options as"),
                'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
                'note' => __('[Closed] option not applicable on [Slide Down] Sidebar Style. <br/> The [Closed] option is recommended if you’re using the Horizontal Design for the Layered Navigation'),
                'values' => $value,
                'value' => $this->_getDisplayOption(),
            ]
        );

        $field = $fieldset->addField(
            'wp_visible_options',
            'text',
            [
                'name' => 'wp_visible_options',
                'label' => __("Initial Number of Visible Options"),
                'value' => $this->_getVisibleOptions(),
                'title' => __('The number of attribute option(s) that will be initially visible. Can be used only with Show Attribute Options as - Expandable.'),
                'note' => __('The number of attribute option(s) that will be initially visible. Can be used only with Show Attribute Options as - Expandable. %1', $swatchComment)

            ]
        );

        $field = $fieldset->addField(
            'wp_visible_options_step',
            'select',
            [
                'name' => 'wp_visible_options_step',
                'label' => __("Expandable items behaviour"),
                'values' => [
                    ['value' => 99, 'label' => __('All')],
                    ['value' => 5, 'label' => __('Show 5 more')],
                    ['value' => 10, 'label' => __('Show 10 more')],
                    ['value' => 15, 'label' => __('Show 15 more')],
                ],
                'value' => $this->_getVisibleOptions(),
                'title' => __('Select the number of attribute option(s) to show/hide when using Expand feature.<br> Can be used only with Show Attribute Options as - Expandable.'),
                'note' => __('Select the number of attribute option(s) to show/hide when using Expand feature.<br> Can be used only with Show Attribute Options as - Expandable. %1', $swatchComment)

            ]
        );

        $fieldset->addField(
            'wp_is_multiselect',
            'select',
            [
                'name' => 'wp_is_multiselect',
                'label' => __("Enable Multiselect"),
                'value' => $this->_getIsMultiselect(),
                'values' => [
                    ['value' => 0, 'label' => __('No')],
                    ['value' => 1, 'label' => __('Yes')],
                ],
                'title' => __('Allow to filter multiple options from the same attribute.'),
                'note' => __('Allow to filter multiple options from the same attribute.')

            ]
        );

        $fieldset->addField(
            'wp_show_quantity',
            'select',
            [
                'name' => 'wp_show_quantity',
                'label' => __("Show Item Counter"),
                'value' => $this->_getShowQuantity(),
                'values' => [
                    ['value' => 0, 'label' => __('No')],
                    ['value' => 1, 'label' => __('Yes')],
                ],
                'title' => __('Show item counter next to the current attribute options.'),
                'note' => __('Show item counter next to the current attribute options. %1', $swatchComment)

            ]
        );

        $fieldset->addField(
            'wp_sort_by',
            'select',
            [
                'name' => 'wp_sort_by',
                'label' => __("Sort By"),
                'value' => $this->_getSortBy(),
                'values' => [
                    ['value' => 1, 'label' => __('Position')],
                    ['value' => 2, 'label' => __('Name')],
                ],
                'title' => __('Select the sorting of the current attribute options.'),
                'note' => __('Select the sorting of the current attribute options. %1', $swatchComment)

            ]
        );

        if(!$isSwatchAttr) {
            $fieldset->addField(
                'wp_instant_search',
                'select',
                [
                    'name' => 'wp_instant_search',
                    'label' => __("Enable options filter on Desktop"),
                    'value' => $this->_getInstantSearch(),
                    'values' => [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')],
                    ],
                    'title' => __('Enable search functionality for attribute options on desktop.'),
                    'note' => __('Enable search functionality for attribute options on desktop. %1', $swatchComment)

                ]
            );

            $fieldset->addField(
                'wp_instant_search_mobile',
                'select',
                [
                    'name' => 'wp_instant_search_mobile',
                    'label' => __("Enable options filter on Mobile"),
                    'value' => $this->_getInstantSearchMobile(),
                    'values' => [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')],
                    ],
                    'title' => __('Enable search functionality for attribute options on mobile.'),
                    'note' => __('Enable search functionality for attribute options on mobile. %1', $swatchComment)

                ]
            );
        }

        $fieldset->addField(
            'wp_category_visibility',
            'select',
            [
                'name' => 'wp_category_visibility',
                'label' => __("Category Visibility"),
                'value' => $this->_getCategoryVisibility(),
                'values' => [
                    ['value' => 0, 'label' => __('Visible in all categories')],
                    ['value' => 1, 'label' => __('Visible only in the following categories')],
                    ['value' => 2, 'label' => __('Not visible in the following categories')],
                ],
                'title' => __('Select visibility behaviour in categories for the current attribute.'),
                'note' => __('Select visibility behaviour in categories for the current attribute.')

            ]
        );

        $fieldset->addField(
            'wp_category_ids',
            'multiselect',
            [
                'name' => 'wp_category_ids[]',
                'label' => __("Select Categories"),
                'value' => $this->_getCategoryIds(),
                'values' => $this->categoriesToOptionArray(),
                'title' => __('Select categories.'),
                'note' => __('Select categories.')

            ]
        );



        if ($field) {
            $field->setAfterElementHtml(
                "<script>
                   //<![CDATA[
                       require(['jquery', 'jquery/ui'], function($){
                            var acceptedTypeArray = ['select','multiselect','price','swatch_visual','swatch_text'],
                                wpDisplayOptionEl = $('#wp_display_options'),
                                wpVisibleOptionsEl = $('#wp_visible_options'),
                                wpVisibleOptionsStepEl = $('#wp_visible_options_step'),
                                wpIsMultiselectEl = $('#wp_is_multiselect'),
                                wpShowQuantityEl = $('#wp_show_quantity'),
                                wpSortByEl = $('#wp_sort_by'),
                                wpIsD = $('#wp_instant_search'),
                                wpIsM = $('#wp_instant_search_mobile'),
                                mageFrontendInpEl = $('#frontend_input'),
                                categoryVisibilityEl = $('#wp_category_visibility'),
                                categoryIdsElement = $('#wp_category_ids'),
                                categoryIdsEl = $('.field-wp_category_ids'),

                                displayOpt = '" . $this->_getDisplayOption() . "',
                                visibleOpt = '" . $this->_getVisibleOptions() . "',
                                visibleOptStep = '" . $this->_getVisibleOptionsStep() . "',
                                isMultiselect = '" . $this->_getIsMultiselect() . "',
                                showQuantity = '" . $this->_getShowQuantity() . "',
                                sortBy = '" . $this->_getSortBy() . "';
                                instantSearchD = '" . $this->_getInstantSearch() . "';
                                instantSearchM = '" . $this->_getInstantSearchMobile() . "';
                                categoryVisibility = '".$this->_getCategoryVisibility()."';
                                categoryIds = '".$this->_getCategoryIds()."';
                                categoryIdsArray = categoryIds.split(',');
                                isSwatch = '".$isSwatchAttr."';


                                wpDisplayOptionEl.val(displayOpt);
                                wpVisibleOptionsEl.val(visibleOpt);
                                wpVisibleOptionsStepEl.val(visibleOptStep);
                                wpIsMultiselectEl.val(isMultiselect);
                                wpShowQuantityEl.val(showQuantity);
                                wpSortByEl.val(sortBy);
                                wpIsD.val(instantSearchD);
                                wpIsM.val(instantSearchM);
                                categoryVisibilityEl.val(categoryVisibility);
                                categoryIdsElement.val(categoryIdsArray);

                                setWpVisibility();

                            categoryVisibilityEl.change(function() {
                                if(categoryVisibilityEl.val() != 0) {
                                    categoryIdsEl.show();
                                } else {
                                    categoryIdsEl.hide();
                                }
                            })



                            wpDisplayOptionEl.change(function(){

                                if($(this).val() == 2) {
                                    enableElement(wpVisibleOptionsEl);
                                    enableElement(wpVisibleOptionsStepEl);
                                    wpVisibleOptionsEl.val(visibleOpt);
                                    wpVisibleOptionsStepEl.val(visibleOptStep);
                                } else {
                                    wpVisibleOptionsEl.val(99);
                                    wpVisibleOptionsStepEl.val(99);
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                }
                            });

                            mageFrontendInpEl.change(function(){
                                var selVal = $(this).val();
                                if($.inArray(selVal, acceptedTypeArray) !== -1) {
                                    enableElement(wpDisplayOptionEl);
                                    enableElement(wpIsMultiselectEl);
                                    enableElement(wpShowQuantityEl);
                                    enableElement(wpSortByEl);
                                    if(wpDisplayOptionEl.val() == 2){
                                       enableElement(wpVisibleOptionsEl);
                                       enableElement(wpVisibleOptionsStepEl);
                                    } else {
                                        disableElement(wpVisibleOptionsEl);
                                        disableElement(wpVisibleOptionsStepEl);
                                    }
                                } else {
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                    disableElement(wpDisplayOptionEl);
                                    disableElement(wpIsMultiselectEl);
                                    disableElement(wpShowQuantityEl);
                                    disableElement(wpSortByEl);
                                }
                            });

                            /**
                            * set wp fields property(disabled)
                            */
                            function setWpVisibility(){
                                var elVal = mageFrontendInpEl.val();

                                if(categoryVisibilityEl.val() != 0) {
                                    categoryIdsEl.show();
                                } else {
                                    categoryIdsEl.hide();
                                }


                                if($.inArray(elVal, acceptedTypeArray) !== -1) {
                                    enableElement(wpDisplayOptionEl);
                                    enableElement(wpIsMultiselectEl);
                                    enableElement(wpShowQuantityEl);
                                    enableElement(wpSortByEl);

                                } else {
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                    disableElement(wpDisplayOptionEl);
                                    disableElement(wpIsMultiselectEl);
                                    disableElement(wpShowQuantityEl);
                                    disableElement(wpSortByEl);
                                }

                                if(wpDisplayOptionEl.val() != 2){
                                   disableElement(wpVisibleOptionsEl);
                                   disableElement(wpVisibleOptionsStepEl);
                                }

                                if(isSwatch) {
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                    disableElement(wpShowQuantityEl);
                                    disableElement(wpSortByEl);
                                    disableElement(wpIsD);
                                    disableElement(wpIsM);
                                    disableElement(wpIsM);
                                }


                            }

                            /**
                            * set element as disabled
                            * @param element
                            */
                            function disableElement(element) {
                                element.prop('disabled', true);
                            }

                            /**
                            * remove 'disabled' attribute from element
                            * @param element
                            */
                            function enableElement(element) {
                                element.removeAttr('disabled');
                            }
                        });
                   //]]>
             </script>"
            );

        }
    }

    protected function _isSwatchAttr() {
        $attributeId = $this->_request->getParam('attribute_id');

        if ($attributeId) {
            $attr = $this->_attributeModel->load($attributeId);
            if(!$attr->getAdditionalData()) {
                return false;
            }
            $addititonalData = $attr->getAdditionalData();
            $attrData = json_decode($addititonalData, true);
            if(isset($attrData['swatch_input_type'])) {
                return true;
            }
            return true;
        }

        return false;
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    /**
     * @return $this|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getWpAttributeOptionValues()
    {
        $attributeId = $this->_request->getParam('attribute_id');

        if ($attributeId) {
            $this->_wpAttributeObj = $this->_wpModel->getDisplayOptionsByAttribute($attributeId);
        }

        return $this;
    }

    /**
     * Get attribute 'display_option' value
     * @return int
     */
    protected function _getDisplayOption()
    {
        $val = AttributeOptions::DISPLAY_OPTION_DEF_VAL;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getDisplayOption();
            }

        }

        return $val;
    }

    /**
     * Get attribute 'visible_options' value
     * @return int
     */
    protected function _getVisibleOptions()
    {
        $val = '';
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getVisibleOptions();
            }

        }

        return $val;
    }

    /**
     * Get attribute 'visible_options' value
     * @return int
     */
    protected function _getVisibleOptionsStep()
    {
        $val = '';
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getVisibleOptionsStep();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'is_multiselect' value
     * @return int
     */
    protected function _getIsMultiselect()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getIsMultiselect();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'show_quantity' value
     * @return int
     */
    protected function _getShowQuantity()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getShowQuantity();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'sort_by' value
     * @return int
     */
    protected function _getSortBy()
    {
        $val = 1;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getSortBy();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'instant_search' value
     * @return int
     */
    protected function _getInstantSearch()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getInstantSearch();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'instant_search_mobile' value
     * @return int
     */
    protected function _getInstantSearchMobile()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getInstantSearchMobile();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'category_visibility' value
     * @return int
     */
    protected function _getCategoryVisibility()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getCategoryVisibility();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'category_visibility' value
     * @return int
     */
    protected function _getCategoryIds()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getCategoryIds();
            }
        }

        return $val;
    }

    /**
     * @param array $result
     * @param string $prefix
     * @param array $category
     * @param bool $ignore
     */
    protected function recursiveCategories(&$result, $prefix, $category, $ignore = false)
    {
        if (!$ignore) {
            $result[$prefix . $category['id']] =  __($category['text']);
            $prefix = $prefix . $category['id'] . '_';
        }
        if (isset($category['children'])) {
            foreach ($category['children'] as $categ) {
                $this->recursiveCategories($result, $prefix, $categ);
            }
        }
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function categoriesArray()
    {
        $categories = $this->adminCategoryTree->getTree();
        $categoryList = [];

        foreach ($categories as $category) {
            $this->recursiveCategories($categoryList, '', $category, true);
        }

        return $categoryList;
    }

    /**
     * @return array
     */
    public function categoriesToOptionArray()
    {
        $arr = $this->categoriesArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {
            $catIdArr = explode('_', $key);
            if(count($catIdArr) == 1) {
                $ret[] = [
                    'value' => $key,
                    'label' => $value,
                    'style' => 'font-weight:bold'
                ];
            } elseif(count($catIdArr) == 2) {
                $ret[] = [
                    'value' => $catIdArr[1],
                    'label' => $value,
                    'style' => 'padding-left: 25px'
                ];
            } elseif(count($catIdArr) == 3) {
                $ret[] = [
                    'value' => $catIdArr[2],
                    'label' => $value,
                    'style' => 'padding-left: 45px'
                ];
            } else {
                $ret[] = [
                    'value' => $catIdArr[3],
                    'label' => $value,
                    'style' => 'padding-left: 65px'
                ];
            }

        }

        return $ret;
    }

    /**
     * @param $category
     * @return array
     */
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }
}

<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Adminhtml\Model\Category\Attribute\Source;

class Sortby
{
    /**
     * @var \WeltPixel\AdvanceCategorySorting\Helper\Data
     */
    protected $_helper;

    /**
     * Sortby constructor.
     * @param \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
     */
    public function __construct(\WeltPixel\AdvanceCategorySorting\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Add new options to available_sort_by config from category edit
     *
     * @param \Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject
     * @param $result
     * @return array
     */
    public function afterGetAllOptions(\Magento\Catalog\Model\Category\Attribute\Source\Sortby $subject, $result)
    {
        if ($this->_helper->getConfigValue('general', 'enable')) {
            $options = $this->_helper->getAllConfigValues('general', $this->_helper->getStoreId());

            $removed = [];
            $newOptions = [];
            foreach ($options as $code => $data) {
                if ($data['enable']) {
                    $key = $this->_helper->getSortOrder($data, $newOptions);
                    if ($data['both_direction']) {
                        $bothOptions = $this->_createBothDirectionLabels($code, $data, $key);
                        foreach ($bothOptions as $boKey => $val) {
                            $keyExists = array_key_exists($boKey, $newOptions);
                            if ($keyExists) {
                                $newKey = $this->_helper->_incrementSortOrder($boKey, $newOptions);
                                $currentOption = $newOptions[$boKey];
                                unset($newOptions[$boKey]);
                                $newOptions[$newKey] = $currentOption;
                            }
                        }
                        $newOptions += $bothOptions;
                    } else {
                        $newOptions[$key] = [
                            'label' => __($data['name']),
                            'value' => $code
                        ];
                    }
                } else {
                    $removed[] = $code;
                }
            }

            /**
             * sort $newOptions by key
             * remove duplicated and disabled options
             */
            ksort($newOptions);
            foreach ($newOptions as $option) {
                foreach ($result as $key => $values) {
                    $optionValue = str_replace(['~desc', '~asc'], [''], $option['value']);
                    if ($values['value'] == $optionValue || in_array($values['value'], $removed)) {
                        unset($result[$key]);
                        break;
                    }
                }
            }

            return array_merge($newOptions, $result);
        }

        return $result;
    }

    /**
     * Make option available in both directions: asc and desc
     *
     * @param $code
     * @param $data
     * @param $key
     * @return array
     */
    public function _createBothDirectionLabels($code, $data, $key)
    {
        $options = [];
        $label = $data['name'];

        switch ($code) {
            case 'name':
                $options[$key] = [
                    'label' => __($label . ' (A to Z)'),
                    'value' => $code . '~asc'
                ];
                $key++;
                $options[$key] = [
                    'label' => __($label . ' (Z to A)'),
                    'value' => $code . '~desc'
                ];
                break;
            case 'price':
                $options[$key] = [
                    'label' => __($label . ' (Low to High)'),
                    'value' => $code . '~asc'
                ];
                $key++;
                $options[$key] = [
                    'label' => __($label . ' (High to Low)'),
                    'value' => $code . '~desc'
                ];
                break;
            default:
                $options[$key] = [
                    'label' => __($label),
                    'value' => $code
                ];
                break;
        }

        return $options;
    }
}

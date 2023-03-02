<?php

namespace WeltPixel\ProductPage\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ProductPage
 *
 * @package WeltPixel\ProductPage\Model\Config\Source
 */
class TabsVersion implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const LEFT_WITH_BORDER  = '0';
    const CENTER_NO_BORDER  = '1';
    const CENTER_STICKY_TABS  = '2';

    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::LEFT_WITH_BORDER => __('Version 1 - aligned left with border'),
            self::CENTER_NO_BORDER => __('Version 2 - centered without border'),
            self::CENTER_STICKY_TABS => __('Version 3 - centered sticky')
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach ($this->getAvailableModes() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {
        $options = $this->getAvailableModes();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}

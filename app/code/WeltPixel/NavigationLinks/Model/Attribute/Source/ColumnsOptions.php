<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ColumnsOptions
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class ColumnsOptions   implements SourceInterface, OptionSourceInterface
{
    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        return [
            ['value' => '2', 'label' => '2'],
            ['value' => '3', 'label' => '3'],
            ['value' => '4', 'label' => '4'],
            ['value' => '5', 'label' => '5'],
            ['value' => '6', 'label' => '6']
        ];
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value) {
        $options = $this->getAllOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }
}


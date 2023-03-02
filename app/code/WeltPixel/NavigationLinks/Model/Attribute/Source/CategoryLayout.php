<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;


/**
 * Class CategoryLayout
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class CategoryLayout implements SourceInterface, OptionSourceInterface
{

    const LAYOUT_DEFAULT = 'default';
    const LAYOUT_IMAGES = 'subcategories_images';

    public function getAvailableLayouts() {
        return [
            self::LAYOUT_DEFAULT => __('Default'),
            self::LAYOUT_IMAGES  => __('Subcategories with images')
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        $result = [];

        foreach ($this->getAvailableLayouts() as $index => $value) {
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
    public function getOptionText($value) {
        $options = $this->getAvailableLayouts();

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

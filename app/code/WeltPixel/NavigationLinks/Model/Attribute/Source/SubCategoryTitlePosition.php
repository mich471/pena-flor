<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SubCategoryTitlePosition
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class SubCategoryTitlePosition implements SourceInterface, OptionSourceInterface
{
    const POSITION_NONE = 'none';
    const POSITION_UNDER_IMAGE = 'under_image';
    const POSITION_ABOVE_IMAGE = 'above_image';

    public function getAvailablePositions() {
        return [
            self::POSITION_NONE => __('Don\'t display'),
            self::POSITION_UNDER_IMAGE  => __('Under Image'),
            self::POSITION_ABOVE_IMAGE  => __('Above Image')
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
        $result = [];

        foreach ($this->getAvailablePositions() as $index => $value) {
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
        $options = $this->getAvailablePositions();

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

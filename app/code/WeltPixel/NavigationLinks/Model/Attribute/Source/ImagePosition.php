<?php


namespace WeltPixel\NavigationLinks\Model\Attribute\Source;


use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;


/**
 * Class ImagePosition
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class ImagePosition implements SourceInterface, OptionSourceInterface
{
    const ABOVE = 'top';
    const BELOW = 'bottom';
    const LEFT = 'left';
    const RIGHT = 'right';

    public function getAvailableLayouts()
    {
        return [
            self::ABOVE => __('Top'),
            self::BELOW  => __('Bottom'),
            self::LEFT  => __('Left'),
            self::RIGHT  => __('Right')
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
    public function getOptionText($value)
    {
        $options = $this->getAvailableLayouts();

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

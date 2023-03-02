<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LabelPosition
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class UnderlineOptions implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const NONE  = 'none';
    const UNDERLINE_LEFT_RIGHT  = 'left-right';
    const UNDERLINE_OUTWARDS  = 'outwards';
    const UNDERLINE_BLOCK  = 'bottom-fade-in';


    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::NONE => __('None'),
            self::UNDERLINE_LEFT_RIGHT => __('Left-Right'),
            self::UNDERLINE_OUTWARDS => __('Outwards'),
            self::UNDERLINE_BLOCK => __('Block')
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

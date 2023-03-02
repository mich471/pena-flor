<?php
namespace WeltPixel\NavigationLinks\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LabelPosition
 * @package WeltPixel\NavigationLinks\Model\Attribute\Source
 */
class LabelPosition implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const ALIGN_CENTER  = 'center';
    const ALIGN_TOP  = 'top';


    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::ALIGN_CENTER => __('Center-Right'),
            self::ALIGN_TOP => __('Top-Right'),
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions() {
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
    public function getOptionText($value) {
        $options = $this->getAvailableModes();

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

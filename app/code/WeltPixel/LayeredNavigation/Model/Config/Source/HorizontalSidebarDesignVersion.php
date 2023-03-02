<?php


namespace WeltPixel\LayeredNavigation\Model\Config\Source;


use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class HorizontalSidebarDesignVersion implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const VERSION_1  = 'v1';
    const VERSION_2 = 'v2';
    const VERSION_3 = 'v3';


    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::VERSION_1 => __('Version 1'),
            self::VERSION_2 => __('Version 2'),
            self::VERSION_3 => __('Version 3'),
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

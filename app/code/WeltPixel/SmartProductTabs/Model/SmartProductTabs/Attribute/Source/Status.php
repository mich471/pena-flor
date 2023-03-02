<?php
namespace WeltPixel\SmartProductTabs\Model\SmartProductTabs\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package WeltPixel\SmartProductTabs\Model\SmartProductTabs\Attribute\Source
 */
class Status implements OptionSourceInterface
{
    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ACTIVE     => __('Active'),
            self::STATUS_INACTIVE     => __('Inactive')
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

        foreach ($this->getAvailableStatuses() as $index => $value) {
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
        $options = $this->getAvailableStatuses();

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

<?php

namespace WeltPixel\UserProfile\Model\Field;

/**
 * Class Gender
 * @package WeltPixel\UserProfile\Model\Field
 */
class Gender
{
    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'male' => __('Male'),
            'female' => __('Female')
        ];
    }

    /**
     * @param string $value
     * @return string mixed
     */
    public function getOptionName($value)
    {
        $availableOptions = $this->getOptions();

        if (isset($availableOptions[$value])) {
            return $availableOptions[$value];
        }

        return $value;
    }
}

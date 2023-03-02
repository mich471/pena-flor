<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Amasty\Feed\Model\Feed;
use Magento\Framework\Option\ArrayInterface;

class Compress implements ArrayInterface
{
    /**#@+
     * Compressing types
     */
    public const COMPRESS_NONE = '';
    public const COMPRESS_ZIP = 'zip';
    public const COMPRESS_GZ = 'gz';
    public const COMPRESS_BZ = 'bz2';
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [
            self::COMPRESS_NONE => __('None'),
            self::COMPRESS_ZIP => __('Zip'),
            self::COMPRESS_GZ => __('Gz'),
            self::COMPRESS_BZ => __('Bz')
        ];

        return $options;
    }
}

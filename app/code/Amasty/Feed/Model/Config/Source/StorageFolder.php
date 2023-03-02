<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FilePath
 */
class StorageFolder implements ArrayInterface
{
    public const MEDIA_FOLDER = 'media';
    public const VAR_FOLDER = 'var';

    public function toOptionArray()
    {
        return [
            ['value' => self::MEDIA_FOLDER, 'label' => __('Use \'pub/media\' folder')],
            ['value' => self::VAR_FOLDER, 'label' => __('Use \'var\' folder')]
        ];
    }
}

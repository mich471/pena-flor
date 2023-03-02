<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    public const MANUALLY  = 'manual';
    public const HOURLY    = 'hourly';
    public const DAILY     = 'daily';
    public const WEEKLY    = 'weekly';
    public const MONTHLY   = 'monthly';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            self::MANUALLY  => __('Manually'),
            self::HOURLY    => __('Hourly'),
            self::DAILY     => __('Daily'),
            self::WEEKLY    => __('Weekly'),
            self::MONTHLY   => __('Monthly'),
        ];
    }
}

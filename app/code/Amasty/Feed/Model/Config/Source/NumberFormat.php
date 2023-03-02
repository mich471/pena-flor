<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Config\Source;

class NumberFormat
{
    /**
     * Points constants
     */
    public const ONE_POINT = 'one';
    public const TWO_POINTS = 'two';
    public const THREE_POINTS = 'three';
    public const FOUR_POINTS = 'four';

    /**
     * Separate constants
     */
    public const DOT = 'dot';
    public const COMMA = 'comma';
    public const SPACE = 'space';

    /**
     * @return array
     */
    public function getAllDecimals()
    {
        return $decimals = [
            self::ONE_POINT => 1,
            self::TWO_POINTS => 2,
            self::THREE_POINTS => 3,
            self::FOUR_POINTS => 4
        ];
    }

    /**
     * @return array
     */
    public function getAllSeparators()
    {
        return $separators = [
            self::DOT => '.',
            self::COMMA => ',',
            self::SPACE => ' ',
        ];
    }
}

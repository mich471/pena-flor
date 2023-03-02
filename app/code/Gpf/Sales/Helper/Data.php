<?php
/**
 * @package Gps\Sales\Helper
 * @author Alejandro Marr <amarr@lyracons.com>
 */

namespace Gpf\Sales\Helper;

/**
 * Class Data
 * @package Gpf\Sales\Helper
 */
class Data
{
    public static function sanitizeInstallments($installments)
    {
        if (strpos($installments, '|') !== false) {
            $sanitized = explode('|', $installments);
            $installments = trim(reset($sanitized));
        }

        return $installments;
    }
}
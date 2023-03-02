<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Exceptions;

use Magento\Framework\Exception\LocalizedException;

class LockProcessException extends LocalizedException
{
    public function __construct(\Magento\Framework\Phrase $phrase = null, \Exception $cause = null, $code = 0)
    {
        if (!$phrase) {
            $phrase = __('Couldn\'t lock process. Feed generation or reindex is in progress. Please wait for '
            . 'the index process or use Force Unlock to run blocked processes.');
        }
        parent::__construct($phrase, $cause, (int) $code);
    }
}

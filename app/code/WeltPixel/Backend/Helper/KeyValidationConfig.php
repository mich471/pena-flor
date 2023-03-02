<?php

namespace WeltPixel\Backend\Helper;

class KeyValidationConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    const VALIDATE_KEY_PATH = "summa_weltpixel/validation/enable";

    /**
     * @return bool|null
     */
    public function execute(): ?bool
    {
        return $this->scopeConfig->getValue(self::VALIDATE_KEY_PATH);
    }
}

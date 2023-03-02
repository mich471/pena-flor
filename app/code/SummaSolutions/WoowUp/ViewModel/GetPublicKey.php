<?php

namespace SummaSolutions\WoowUp\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use SummaSolutions\WoowUp\Helper\Configuration;

class GetPublicKey implements ArgumentInterface
{
    /**
     * @var Configuration
     */
    protected Configuration $configurationHelper;

    /**
     * @param Configuration $configurationHelper
     */
    public function __construct(
        Configuration $configurationHelper
    ) {
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * @return string
     */
    public function execute()
    {
        return $this->configurationHelper->getPublicKey();
    }
}

<?php
namespace WeltPixel\SpeedOptimization\Plugin\View\Asset;

use Magento\Framework\View\Asset\Minification;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;

/**
 * Class MinificationPlugin
 * @package WeltPixel\SpeedOptimization\Plugin\View\Asset
 */
class MinificationPlugin
{
    /**
     * @var SpeedHelper
     */
    protected $speedHelper;

    /**
     * MinificationPlugin constructor.
     * @param SpeedHelper $speedHelper
     */
    public function __construct(
        SpeedHelper $speedHelper
    ) {
        $this->speedHelper = $speedHelper;
    }

    /**
     * @param Minification $subject
     * @param \Closure $proceed
     * @param string $contentType
     * @return bool
     */
    public function aroundIsEnabled(
        Minification $subject,
        \Closure $proceed,
        $contentType
    ) {
        if ($this->speedHelper->isEnabled() && $this->speedHelper->isAdvancedJsBundlingEnabled() && ($contentType === 'js')) {
            return false;
        }
        return $proceed($contentType);
    }
}

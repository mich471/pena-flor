<?php

namespace WeltPixel\SpeedOptimization\Block\Html;

use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Template\Context as ViewElementContext;

class RequireJsCollector extends Template
{
    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var SpeedHelper
     */
    private $speedHelper;


    /** @var  ThemeProviderInterface */
    protected $themeProvider;

    /**
     * Config constructor.
     * @param ViewElementContext $context
     * @param AppState $appState
     * @param HttpRequest $httpRequest
     * @param SpeedHelper $speedHelper
     * @param ThemeProviderInterface $themeProvider
     * @param array $data
     */
    public function __construct(
        ViewElementContext $context,
        AppState $appState,
        HttpRequest $httpRequest,
        SpeedHelper $speedHelper,
        ThemeProviderInterface $themeProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->appState = $appState;
        $this->httpRequest = $httpRequest;
        $this->speedHelper = $speedHelper;
        $this->themeProvider = $themeProvider;
    }

    /**
     * @return string
     */
    public function getPageFullActionName() {
        return $this->httpRequest->getFullActionName();
    }

    /**
     * @return string
     */
    public function getCurrentThemePath() {
        $themeId = $this->_scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $locale = $this->_scopeConfig->getValue(
            \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $theme = $this->themeProvider->getThemeById($themeId);
        return $theme->getThemePath().'/'.$locale;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->appState->getMode() === AppState::MODE_PRODUCTION) {
            return '';
        }

        return parent::_toHtml();
    }
}

<?php

namespace WeltPixel\SpeedOptimization\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    /**
     * @var array
     */
    protected $_optimizationOptions;

    /**
     * Data constructor.
     * @param HelperContext $context
     */
    public function __construct(
        HelperContext $context
    ) {
        parent::__construct($context);
        $this->_optimizationOptions = $this->scopeConfig->getValue('weltpixel_speedoptimization', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_optimizationOptions['general']['enable'];
    }


    /**
     * @return bool
     */
    public function isAdvancedJsBundlingEnabled()
    {
        return (bool)$this->_optimizationOptions['js_optimization']['enable_advanced_bundling'];
    }

    /**
     * @return bool
     */
    public function isJsMoveToBottomEnabled()
    {
        return $this->isEnabled() && (bool)$this->_optimizationOptions['js_optimization']['move_js_to_bottom'];
    }

    /**
     * @return string
     */
    public function moveJsToBottomIgnoredBlocks()
    {
        return $this->_optimizationOptions['js_optimization']['move_js_to_bottom_ignored_blocks'];
    }

    /**
     * @return bool
     */
    public function isCssPreloadEnabled()
    {
        return $this->isEnabled() && (bool)$this->_optimizationOptions['css_optimization']['css_preload'];
    }

    /**
     * @return bool
     */
    public function isCssPreloadEnabledForAll()
    {
        return $this->isEnabled() && (bool)$this->_optimizationOptions['css_optimization']['css_preload_for_all'];
    }

    /**
     * @return array
     */
    public function getInlineHtmlTags()
    {
        return [
            'b',
            'big',
            'i',
            'small',
            'tt',
            'abbr',
            'acronym',
            'cite',
            'code',
            'dfn',
            'em',
            'kbd',
            'strong',
            'samp',
            'var',
            'a',
            'bdo',
            'br',
            'img',
            'map',
            'object',
            'q',
            'span',
            'sub',
            'sup',
            'button',
            'input',
            'label',
            'select',
            'textarea',
            '\?',
        ];
    }

    /**
     * @param $htm
     * @return null|string|string[]
     */
    public function minifyHtml($html)
    {
        $inlineHtmlTags = $this->getInlineHtmlTags();
        $html = preg_replace(
            '#(?<!]]>)\s+</#',
            '</',
            preg_replace(
                '#((?:<\?php\s+(?!echo|print|if|elseif|else)[^\?]*)\?>)\s+#',
                '$1 ',
                preg_replace(
                    '#(?<!' . implode('|', $inlineHtmlTags) . ')\> \<#',
                    '><',
                    preg_replace(
                        '#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre|script)\b)))'
                        . '(?:<(?>textarea|pre|script)\b|\z))#',
                        ' ',
                        preg_replace(
                            '#(?<!:|\\\\|\'|")//(?!\s*\<\!\[)(?!\s*]]\>)[^\n\r]*#',
                            '',
                            preg_replace(
                                '#(?<!:|\'|")//[^\n\r]*(\?\>)#',
                                ' $1',
                                preg_replace(
                                    '#(?<!:)//[^\n\r]*(\<\?php)[^\n\r]*(\s\?\>)[^\n\r]*#',
                                    '',
                                    $html
                                )
                            )
                        )
                    )
                )
            )
        );

        return $html;
    }


    /**
     * @return bool
     */
    public function checkifBrowserPreloadNotSupported()
    {
        $userAgent = $this->_httpHeader->getHttpUserAgent();
        if(strpos($userAgent, 'MSIE') !== FALSE || strpos($userAgent, 'Trident') !== FALSE || strpos($userAgent, 'Firefox') !== FALSE) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isMagentoCriticalCssEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'dev/css/use_css_critical_path',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function getJsOptimizationMethod()
    {
        if (!isset( $this->_optimizationOptions['reqiurejs_bundle_generation']['optimization_method'])) {
            return \WeltPixel\SpeedOptimization\Model\Config\Source\Optimization::OPTIMIZATION_UGLIFY;
        }
        return $this->_optimizationOptions['reqiurejs_bundle_generation']['optimization_method'];
    }
}

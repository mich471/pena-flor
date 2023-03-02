<?php

namespace WeltPixel\SpeedOptimization\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Code\Minifier\Adapter\Js\JShrink;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;
use WeltPixel\SpeedOptimization\Model\Storage;

class BlockAfterToHtml implements ObserverInterface
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var JShrink
     */
    protected $jsShrink;

    /**
     * @var SpeedHelper
     */
    protected $speedHelper;

    /**
     * @var  HttpRequest
     */
    protected $request;

    /**
     * Parent layout of the block
     *
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * BlockAfterToHtml constructor.
     * @param Storage $storage
     * @param HttpRequest $request
     * @param SpeedHelper $speedHelper
     * @param JShrink $jsShrink
     * @param LayoutInterface $layout
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Storage $storage,
        HttpRequest $request,
        SpeedHelper $speedHelper,
        JShrink $jsShrink,
        LayoutInterface $layout,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storage = $storage;
        $this->speedHelper = $speedHelper;
        $this->jsShrink = $jsShrink;
        $this->request = $request;
        $this->layout = $layout;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $speedOptimizationsEabled = $this->speedHelper->isEnabled();
        $jsMoveToBottomEnabled = $this->speedHelper->isJsMoveToBottomEnabled();

        $transport = $observer->getData('transport');
        $html = $transport->getHtml();

        if (!$speedOptimizationsEabled) {
            return $this;
        }

        try {
            $html = preg_replace_callback('/<script.*?>.*?<\/script>/is', function ($matches) use (&$inlineScripts) {
                $inlineScripts = $matches[0];
                return $this->jsShrink->minify($inlineScripts);
            }, $html);
            $transport->setHtml($html);
        } catch (\Exception $ex) {
        }

        if ($jsMoveToBottomEnabled && !$this->request->isAjax() && $this->layout->hasElement('before_body_js')) {
            $block = $observer->getData('block');
            $ignoredBlocks = ['require.js', 'head.additional', 'transparent_iframe', 'wp.gtm.datalayer.scripts', 'wp.ga4.datalayer.scripts'];
            $ignoredBlocksString = $this->speedHelper->moveJsToBottomIgnoredBlocks();
            if (strlen(trim($ignoredBlocksString))) {
                $userIgnoredBlocks = array_map('trim', explode(',', $ignoredBlocksString));
                $ignoredBlocks = array_merge($userIgnoredBlocks, $ignoredBlocks);
            }

            $parentBlock = $block->getParentBlock();
            if ($parentBlock && in_array($parentBlock->getNameInLayout(), $ignoredBlocks)) {
                return $this;
            }
            if (in_array($block->getNameInLayout(), $ignoredBlocks)) {
                return $this;
            }

            $isInlineTranslateEnabled = $this->scopeConfig->isSetFlag(
                'dev/translate_inline/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if ($isInlineTranslateEnabled) {
                $blockTemplate = $block->getTemplate();
                if (strpos($blockTemplate, 'Magento_Translation') !== false) {
                    return $this;
                }
            }

            $inlineScripts = '';
            $html = preg_replace_callback("#<script[^>]*+(?<!text/x-magento-template.)>.*?<\/script>#is", function ($matches) use (&$inlineScripts) {
                if (strpos($matches[0], 'data-ignorejsmove') === false) {
                    $inlineScripts .= $matches[0];
                    return '';
                } else {
                    return str_replace([
                        'data-ignorejsmove="true"',
                        'data-ignorejsmove=\'true\''
                    ], '', $matches[0]);
                }
            }, $html);

            $inlineScripts = $this->storage->getInlineScripts() . $inlineScripts;
            $this->storage->setInlineScripts($inlineScripts);
        }

        $transport->setHtml($html);

        return $this;
    }
}

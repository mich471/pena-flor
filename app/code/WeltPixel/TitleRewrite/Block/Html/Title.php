<?php

namespace WeltPixel\TitleRewrite\Block\Html;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Title extends \Magento\Theme\Block\Html\Title
{
    /**
     * Config path to 'Translate Title' header settings
     */
    private const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    private $registry;
    protected $currentEntity;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $scopeConfig, $data);
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->currentEntity = $this->getCurrentEntity();
    }

    /**
     * @return false|mixed|null
     */
    public function getCurrentEntity()
    {
        $registry = $this->registry;

        if ($registry->registry('current_product')) {
            return $registry->registry('current_product');
        } elseif ($registry->registry('current_category')) {
            return $registry->registry('current_category');
        }

        return false;
    }

    /**
     * Provide own page content heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        if ($this->currentEntity && $this->currentEntity->getTitleRewrite()) {
            return $this->currentEntity->getTitleRewrite();
        }

        $pageTitle = !empty($this->pageTitle) ? $this->pageTitle : $this->pageConfig->getTitle()->getShortHeading();
        return $this->shouldTranslateTitle() ? __($pageTitle) : $pageTitle;
    }

    /**
     * Check if page title should be translated
     *
     * @return bool
     */
    private function shouldTranslateTitle(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HEADER_TRANSLATE_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}

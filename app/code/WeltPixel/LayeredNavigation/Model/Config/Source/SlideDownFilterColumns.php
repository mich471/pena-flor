<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

/**
 * Class SlideDownFilterColumns
 * @package WeltPixel\LayeredNavigation\Model\Config\Source
 */
class SlideDownFilterColumns implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_styles = array(
        '99%' => '1 column',
        '48.5%' => '2 columns',
        '31.9%' => '3 columns',
        '23.4%' => '4 columns',
        '18.4%' => '5 columns',
        '15.2%' => '6 columns',
    );

    /**
     * @var array
     */
    protected $_pearl_styles = array(
        '99%' => '1 column',
        '49%' => '2 columns',
        '32.33%' => '3 columns',
        '24%' => '4 columns',
        '19%' => '5 columns',
        '16.33%' => '6 columns',
    );
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /** @var  ThemeProviderInterface */
    protected $themeProvider;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        if($this->_isPearlTheamActive()) {
            foreach ($this->_pearl_styles as $id => $style) :
                $options[] = array(
                    'value' => $id,
                    'label' => $style
                );
            endforeach;
        } else {
            foreach ($this->_styles as $id => $style) :
                $options[] = array(
                    'value' => $id,
                    'label' => $style
                );
            endforeach;
        }

        return $options;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function _isPearlTheamActive()
    {
        try {
            $store = $this->storeManager->getStore();

            $themeId = $this->scopeConfig->getValue(
                \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );

            $theme = $this->themeProvider->getThemeById($themeId);
            $themePath = $theme->getThemePath();
            if (strpos($themePath, 'Pearl') !== false) {
                return true;
            }

            return false;

        } catch (\Exception $ex) {
            throw new \Exception('Store with id or code ' . $store->getId() . ' not found.');
        }
    }
}
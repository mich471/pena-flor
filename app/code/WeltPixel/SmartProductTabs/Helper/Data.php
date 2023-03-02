<?php
namespace WeltPixel\SmartProductTabs\Helper;

/**
 * Class Data
 * @package WeltPixel\SmartProductTabs\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    protected $_scopeValue = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * @var string
     */
    protected $_tabName = [
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_1',
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_2',
        'weltpixel_smartproducttabs/general/attribute_smartproducttabs_tab_3'
    ];

    /**
     * @return string
     */
    public function getTabNameA()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[0], $this->_scopeValue);
        if (empty($tabName)) {
            return 'Smart Product Tab';
        }
        return $tabName;
    }

    /**
     * @return string
     */
    public function getTabNameB()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[1], $this->_scopeValue);
        if (empty($tabName)) {
            return 'Smart Product Tab';
        }
        return $tabName;
    }

    /**
     * @return string
     */
    public function getTabNameC()
    {
        $tabName = $this->scopeConfig->getValue($this->_tabName[2], $this->_scopeValue);
        if (empty($tabName)) {
            return 'Smart Product Tab';
        }
        return $tabName;
    }

    /**
     * @return bool
     */
    public function isSmartProductTabsEnabled()
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/enable');
    }

    /**
     * @param string $systemTabName
     * @return bool
     */
    public function isChangesEnbledForTab($systemTabName)
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_changes_enable');
    }

    /**
     * @param string $systemTabName
     * @return bool
     */
    public function canShowTab($systemTabName)
    {
        return (bool)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_show');
    }

    /**
     * @param string $systemTabName
     * @return int
     */
    public function getPositionForTab($systemTabName)
    {
        return (int)$this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_position');
    }

    /**
     * @param string $systemTabName
     * @return string
     */
    public function getTitleForTab($systemTabName)
    {
        return $this->scopeConfig->getValue('weltpixel_smartproducttabs/weltpixel_smartproducttabs_grid/tab_' . $systemTabName . '_title');
    }

    /**
     * @param string $systemTabName
     * @return array
     */
    public function getSystemTabOptions($systemTabName)
    {
        $tabName = '';
        switch ($systemTabName) {
            case 'reviews.tab':
                $tabName = 'reviews';
                break;
            case 'product.attributes':
                $tabName = 'moreinformation';
                break;
            case 'product.info.description':
                $tabName = 'details';
                break;
        }
        $tabPosition =  trim($this->getPositionForTab($tabName));
        $tabTitle = trim($this->getTitleForTab($tabName));
        $changeSortOrder = false;
        $changeTitle = false;
        if (strlen($tabPosition)) {
            $changeSortOrder = true;
        }
        if (strlen($tabTitle)) {
            $changeTitle = true;
        }
        return [
            'changes_enabled' => (bool) ($tabName) ? $this->isSmartProductTabsEnabled($tabName) : false,
            'show' =>  (bool) ($tabName) ? $this->canShowTab($tabName) : true,
            'change_sort_order' => (bool) $changeSortOrder,
            'sort_order' => $tabPosition,
            'change_title' => (bool) $changeTitle,
            'title' => $tabTitle
        ];
    }
}

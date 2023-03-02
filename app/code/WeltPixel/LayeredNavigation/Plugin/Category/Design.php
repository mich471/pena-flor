<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Plugin\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\UrlInterface;
use WeltPixel\LayeredNavigation\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use WeltPixel\NavigationLinks\Model\Attribute\Source\CategoryLayout;

/**
 * Class Design
 * @package WeltPixel\LayeredNavigation\Plugin\Category
 */
class Design
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var UrlInterface
     */
    protected $_storeManager;
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * View constructor.
     * @param JsonFactory $resultJsonFactory
     * @param UrlInterface $_storeManager
     * @param Data $wpHelper
     * @param PageFactory $pageFactory
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        UrlInterface $_storeManager,
        Data $wpHelper,
        PageFactory $pageFactory
    )
    {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $_storeManager;
        $this->_wpHelper = $wpHelper;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Design $subject
     * @param \Magento\Framework\DataObject $result
     * @param Category|Product $object
     * @return \Magento\Framework\DataObject
     */
    public function afterGetDesignSettings(\Magento\Catalog\Model\Design $subject, $result, $object)
    {
        if(!$this->_wpHelper->isEnabled()) {
            return $result;
        }
        if ($object instanceof Product) {
            $currentCategory = $object->getCategory();
        } else {
            $currentCategory = $object;
        }

        if (!$currentCategory) {
            return $result;
        }

        $sideBarStyle = $this->_wpHelper->getSidebarStyle();
        if ($sideBarStyle != 3) {
            return $result;
        }

        $result->setPageLayout('1column');

        $pageLayoutHandles = $result->getPageLayoutHandles();
        $horizontalLayoutHandle = ['wpln' => 'horizontalnavigation'];
        if (is_array($pageLayoutHandles)) {
            $pageLayoutHandles['wpln'] = 'horizontalnavigation';
        } else {
            $pageLayoutHandles = $horizontalLayoutHandle;
        }
        $result->setPageLayoutHandles($pageLayoutHandles);
        return $result;
    }
}

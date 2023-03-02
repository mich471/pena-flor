<?php
namespace WeltPixel\Sitemap\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddMetaTags
 * @package WeltPixel\Sitemap\Observer
 */
class AddMetaTags implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_pageConfig;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \WeltPixel\Sitemap\Model\IndexFollowBuilder
     */
    protected $_indexFollowBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * AddMetaTags constructor.
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->_pageConfig = $pageConfig;
        $this->_request = $request;
        $this->_layout = $layout;
        $this->_indexFollowBuilder = $indexFollowBuilder;
        $this->_registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $fullActionName = $this->_request->getFullActionName();
        switch ($fullActionName) {
            case 'cms_index_index':
            case 'cms_page_view':
            case 'cms_noroute_index':
                $page = $this->_layout->getBlock('cms_page')->getPage();
                if ($page->getData('wp_enable_index_follow')) {
                    $indexValue = $page->getData('wp_index_value');
                    $followValue = $page->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($page->getData('wp_enable_canonical_url')) {
                    $canonicalUrl = $page->getData('wp_canonical_url');
                    $this->_pageConfig->addRemotePageAsset(
                        $canonicalUrl,
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
                break;
            case 'catalog_category_view':
                $currentCategory = $this->_registry->registry('current_category');
                if ($currentCategory && $currentCategory->getData('wp_enable_index_follow')) {
                    $indexValue = $currentCategory->getData('wp_index_value');
                    $followValue = $currentCategory->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($currentCategory && $currentCategory->getData('wp_enable_canonical_url')) {
                    $canonicalUrl = $currentCategory->getData('wp_canonical_url');
                    $this->_pageConfig->addRemotePageAsset(
                        $canonicalUrl,
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
                break;
            case 'catalog_product_view':
                $currentProduct = $this->_registry->registry('current_product');
                if ($currentProduct && $currentProduct->getData('wp_enable_index_follow')) {
                    $indexValue = $currentProduct->getData('wp_index_value');
                    $followValue = $currentProduct->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($currentProduct && $currentProduct->getData('wp_enable_canonical_url')) {
                    $canonicalUrl = $currentProduct->getData('wp_canonical_url');
                    $this->_pageConfig->addRemotePageAsset(
                        $canonicalUrl,
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
                break;
        }
    }
}

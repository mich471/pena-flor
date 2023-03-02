<?php

namespace WeltPixel\GoogleCards\Block;

use \Magento\Framework\Session\Generic as GenericSession;

/**
 * Class Breadcrumbs
 * @package WeltPixel\GoogleCards\Block
 */
class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \WeltPixel\GoogleCards\Model\BreadcrumbStorage
     */
    protected $_breadcrumbStorage;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var \WeltPixel\GoogleCards\Helper\Data
     */
    protected $_googleCardsHelper;

    /**
     * @var GenericSession
     */
    protected $_genericSession;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * @var \WeltPixel\GoogleCards\ViewModel\Product\Breadcrumbs
     */
    protected $_breadcrumbsViewModel;

    /**
     * @var bool
     */
    protected $_productPage = false;

    /**
     * Breadcrumbs constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \WeltPixel\GoogleCards\Model\BreadcrumbStorage $breadcrumbStorage
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \WeltPixel\GoogleCards\Helper\Data $googleCardsHelper
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param GenericSession $genericSession
     * @param \WeltPixel\GoogleCards\ViewModel\Product\Breadcrumbs $breadcrumbViewModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \WeltPixel\GoogleCards\Model\BreadcrumbStorage $breadcrumbStorage,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \WeltPixel\GoogleCards\Helper\Data $googleCardsHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        GenericSession $genericSession,
        \WeltPixel\GoogleCards\ViewModel\Product\Breadcrumbs $breadcrumbViewModel,
        array $data = []
    )
    {
        $this->_breadcrumbStorage = $breadcrumbStorage;
        $this->_catalogHelper = $catalogHelper;
        $this->_googleCardsHelper = $googleCardsHelper;
        $this->_genericSession = $genericSession;
        $this->_categoryRepository = $categoryRepository;
        $this->_breadcrumbsViewModel = $breadcrumbViewModel;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Breadcrumb data from session
     * @return mixed
     */
    public function getCrumbs()
    {
        $rootCategoryId = $this->_getRootCategoryId();
        $breadcrumbType = $this->_googleCardsHelper->getBreadcrumbType();
        if ($this->_productPage && $breadcrumbType == \WeltPixel\GoogleCards\Model\Config\Source\BreadcrumbsType::BREADCRUMB_FULL) {
            $path = [];
            $path['home'] = [
                'label' => __('Home'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ];
            $latestCategoryId = $this->_genericSession->getLatestCategoryId();
            $product = $this->_catalogHelper->getProduct();
            $productCategoryIds = array_diff($product->getCategoryIds(), [$rootCategoryId]);
            $productCategoryId = $latestCategoryId;
            if (is_array($productCategoryIds) && !in_array($latestCategoryId, $productCategoryIds)) {
                $productCategoryId = array_pop($productCategoryIds);
            }

            try {
                $category = $this->_categoryRepository->get($productCategoryId);
            } catch (\Exception $ex) {
                return [];
            }
            $pathInStore = $category->getPathInStore();
            $pathIds = array_reverse(explode(',', $pathInStore));

            $categories = $category->getParentCategories();

            // add category path breadcrumb
            foreach ($pathIds as $categoryId) {
                if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                    $path['category' . $categoryId] = [
                        'label' => $categories[$categoryId]->getName(),
                        'link' => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : ''
                    ];
                }
            }

            $breadcrumbsFrontendPath = $path;
            $breadcrumbsFrontendPath['product'] = ['label' => $product->getName()];
            $this->_breadcrumbsViewModel->setBreadCrumbs($breadcrumbsFrontendPath);

            return $path;
        }
        $crumbs = $this->_breadcrumbStorage->getBreadcrumbData() ? $this->_breadcrumbStorage->getBreadcrumbData() : [];

        $crumbsWithLinks  = [];
        foreach ($crumbs as $crumb) {
            if (isset($crumb['link']) && strlen($crumb['link'])) {
                $crumbsWithLinks[] = $crumb;
            }
        }

        return $crumbsWithLinks;
    }

    /**
     * @return \Magento\Framework\View\Element\Template|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->_catalogHelper->getProduct()) {
            $this->_productPage = true;
            $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
        }
        if ($this->_catalogHelper->getCategory()) {
            $this->_genericSession->setLatestCategoryId($this->_catalogHelper->getCategory()->getId());
        }
    }

    /**
     * Check is category link
     *
     * @param int $categoryId
     * @return bool
     */
    protected function _isCategoryLink($categoryId)
    {
        if ($this->_catalogHelper->getProduct()) {
            return true;
        }
        if ($categoryId != $this->_catalogHelper->getCategory()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getRootCategoryId()
    {
        return $this->_storeManager->getStore()->getRootCategoryId();
    }
}

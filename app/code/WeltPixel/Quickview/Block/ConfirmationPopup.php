<?php
namespace WeltPixel\Quickview\Block;

use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\CustomerData\ItemPoolInterface;
use WeltPixel\Quickview\Model\Config\Source\CarouselType;

/**
 * Class ConfirmationPopup
 * @package WeltPixel\Quickview\Block
 */
class ConfirmationPopup extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $quote = null;

    /**
     * @var int|float
     */
    protected $summeryCount;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Url
     */
    protected $catalogUrl;

    /**
     * @var ItemPoolInterface
     */
    protected $itemPoolInterface;

    /**
     * @var \WeltPixel\QuickView\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $checkoutCart;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * Catalog product visibility
     *
     * @var ProductVisibility
     */
    protected $catalogProductVisibility;

    /**
     * @var string
     */
    protected $productType = '';

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    protected $product;

    /**
     * @param \WeltPixel\Quickview\Helper\Data $helper
     * @param ItemPoolInterface $itemPoolInterface
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param ProductRepository $productRepository
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ProductVisibility $catalogProductVisibility
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \WeltPixel\Quickview\Helper\Data $helper,
        ItemPoolInterface $itemPoolInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        ProductRepository $productRepository,
        \Magento\Catalog\Model\Config $catalogConfig,
        ProductVisibility $catalogProductVisibility,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->itemPoolInterface = $itemPoolInterface;
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
        $this->checkoutCart = $checkoutCart;
        $this->catalogUrl = $catalogUrl;
        $this->productRepository = $productRepository;
        $this->catalogConfig = $catalogConfig;
        $this->catalogProductVisibility = $catalogProductVisibility;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getPopupDetails()
    {
        $totals = $this->getQuote()->getTotals();
        $subtotalAmount = $totals['subtotal']->getValue();
        return [
            'summary_count' => $this->getSummaryCount(),
            'subtotalAmount' => $subtotalAmount,
            'subtotal' => isset($totals['subtotal'])
                ? $this->checkoutHelper->formatPrice($subtotalAmount)
                : 0,
            'items' => $this->getRecentItems(),
            'static_block_content' => $this->getStaticBlockContent(),
            'display_carousel' => (bool)$this->_helper->displayCarousel(),
            'carousel_title' => $this->_helper->getCarouselTitle(),
            'popup_title' => $this->_helper->getAjaxCartPopupTitle(),
            'carousel_products' => $this->getCarouselProducts(),
            'product_type' => $this->productType,
            'product' => $this->product
        ];
    }

    /**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return int|float
     */
    protected function getSummaryCount()
    {
        if (!$this->summeryCount) {
            $this->summeryCount = $this->checkoutCart->getSummaryQty() ?: 0;
        }
        return $this->summeryCount;
    }

    /**
     * Return customer quote items
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    protected function getAllQuoteItems()
    {
        $quoteVisibleItems = $this->getQuote()->getAllVisibleItems();
        usort($quoteVisibleItems, function ($a, $b) {
            if ($a->getData('updated_at') == $b->getData('updated_at')) {
                return ($a->getData('created_at') < $b->getData('created_at')) ? -1 : 1;
            }
            return ($a->getData('updated_at') < $b->getData('updated_at')) ? -1 : 1;
        });

        return $quoteVisibleItems;
    }

    /**
    * Get array of last added items
    *
    * @return \Magento\Quote\Model\Quote\Item[]
    */
    protected function getRecentItems()
    {
        $items = [];
        if (!$this->getSummaryCount()) {
            return $items;
        }

        foreach (array_reverse($this->getAllQuoteItems()) as $item) {
            /* @var $item \Magento\Quote\Model\Quote\Item */
            if (!$item->getProduct()->isVisibleInSiteVisibility()) {
                $product =  $item->getOptionByCode('product_type') !== null
                    ? $item->getOptionByCode('product_type')->getProduct()
                    : $item->getProduct();

                $products = $this->catalogUrl->getRewriteByProductStore([$product->getId() => $item->getStoreId()]);
                if (isset($products[$product->getId()])) {
                    $urlDataObject = new \Magento\Framework\DataObject($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($urlDataObject);
                }
            }
            $items[] = $this->itemPoolInterface->getItemData($item);
        }
        return $items;
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCarouselProducts()
    {
        $lastAddedProductId = $this->getLastAddedProductId();
        if (!$lastAddedProductId) {
            return null;
        }
        $lastAddedProduct = $this->productRepository->getById($lastAddedProductId);
        $this->product = $lastAddedProduct;
        $this->productType = $lastAddedProduct->getTypeId();

        if (!$this->_helper->displayCarousel()) {
            return null;
        }

        $productCollection = null;
        $carouselType = $this->_helper->getCarouselType();
        $this->setData('carousel_type', $carouselType);

        switch ($carouselType) {
            case CarouselType::TYPE_RELATED:
                $productCollection = $lastAddedProduct->getRelatedProductCollection()->addAttributeToSelect(
                    'required_options'
                )->setPositionOrder()->addStoreFilter();
                break;
            case CarouselType::TYPE_UPSELL:
                $productCollection = $lastAddedProduct->getUpSellProductCollection()->setPositionOrder()->addStoreFilter();
                break;
            case CarouselType::TYPE_CROSSELL:
                $productCollection = $lastAddedProduct->getCrossSellProductCollection()->setPositionOrder()->addStoreFilter();
                break;
        }

        $this->_addProductAttributesAndPrices($productCollection);
        $productCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $productCollection->load();
        foreach ($productCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $productCollection;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStaticBlockContent()
    {
        if (!$this->_helper->displayCmsBlock()) {
            return '';
        }

        $cmsBlockId = $this->_helper->getCmsBlockId();
        if (!$cmsBlockId) {
            return '';
        }

        return $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($cmsBlockId)
            ->toHtml();
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}

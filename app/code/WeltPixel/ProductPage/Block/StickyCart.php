<?php
namespace WeltPixel\ProductPage\Block;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Model\Product;
use WeltPixel\ProductPage\Model\Config\Source\DisplayOn;

/**
 * Class StickyCart
 * @package WeltPixel\ProductPage\Block
 */
class StickyCart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \WeltPixel\ProductPage\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $_imageBuilder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \WeltPixel\ProductPage\Helper\Data $wpHelper
     * @param \Magento\Catalog\Block\Product\Context $productContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\ProductPage\Helper\Data $wpHelper,
        \Magento\Catalog\Block\Product\Context $productContext,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_wpHelper = $wpHelper;
        $this->_imageBuilder = $productContext->getImageBuilder();
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->_imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @param string $optionName
     * @return bool
     */
    protected function canDisplayOption($optionName)
    {
        return ($this->_wpHelper->isStickyAddToCartDesktopEnabled() &&
            in_array($optionName, $this->_wpHelper->getDisplayStickyAddToCartOnDesktopOptions()) ||
            $this->_wpHelper->isStickyAddToCartMobileEnabled() &&
            in_array($optionName, $this->_wpHelper->getDisplayStickyAddToCartOnMobileOptions())
        );
    }

    /**
     * @param string $optionName
     * @return string
     */
    protected function getDisplayClass($optionName)
    {
        $classNames = [];
        if ($this->_wpHelper->isStickyAddToCartDesktopEnabled() &&
            in_array($optionName, $this->_wpHelper->getDisplayStickyAddToCartOnDesktopOptions())) {
            $classNames[] = 'desktop';
        }
        if ($this->_wpHelper->isStickyAddToCartMobileEnabled() &&
            in_array($optionName, $this->_wpHelper->getDisplayStickyAddToCartOnMobileOptions())) {
            $classNames[] = 'mobile';
        }

        if (count($classNames) != 1) {
            return '';
        }

        return 'display-only-' . $classNames[0];

    }

    /**
     * @return bool
     */
    public function displayProductImage()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_IMAGE);
    }

    /**
     * @return bool
     */
    public function displayProductName()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_NAME);
    }

    /**
     * @return bool
     */
    public function displayProductReview()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_REVIEW);
    }

    /**
     * @return bool
     */
    public function displayProductPrice()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_PRICE);
    }

    /**
     * @return bool
     */
    public function displayAddToCartButton()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_ADDTOCART);
    }

    /**
     * @return bool
     */
    public function displayAddToWishlist()
    {
        return $this->canDisplayOption(DisplayOn::DISPLAY_ADDTOWISHLIST);
    }

    /**
     * @return string
     */
    public function getDisplayClassForProductImage()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_IMAGE);
    }

    /**
     * @return string
     */
    public function getDisplayClassForProductName()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_NAME);
    }

    /**
     * @return string
     */
    public function getDisplayClassForProductReview()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_REVIEW);
    }

    /**
     * @return string
     */
    public function getDisplayClassForProductPrice()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_PRICE);
    }

    /**
     * @return string
     */
    public function getDisplayClassForAddToCart()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_ADDTOCART);
    }

    /**
     * @return string
     */
    public function getDisplayClassForAddToWishlist()
    {
        return $this->getDisplayClass(DisplayOn::DISPLAY_ADDTOWISHLIST);
    }


    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->_wpHelper->isStickyAddToCartDesktopEnabled() && !$this->_wpHelper->isStickyAddToCartMobileEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}

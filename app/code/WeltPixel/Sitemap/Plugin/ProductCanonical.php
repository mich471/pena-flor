<?php
namespace WeltPixel\Sitemap\Plugin;

/**
 * Class ProductCanonical
 * @package WeltPixel\Sitemap\Plugin
 */
class ProductCanonical
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * CategoryCanonical constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Helper\Product $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanUseCanonicalTag(\Magento\Catalog\Helper\Product $subject, $result)
    {
        $currentProduct = $this->_registry->registry('current_product');
        if ($currentProduct && $currentProduct->getData('wp_enable_canonical_url')) {
            return false;
        }
        return $result;
    }
}

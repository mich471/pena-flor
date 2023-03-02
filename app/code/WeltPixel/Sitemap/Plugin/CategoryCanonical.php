<?php
namespace WeltPixel\Sitemap\Plugin;

/**
 * Class CategoryCanonical
 * @package WeltPixel\Sitemap\Plugin
 */
class CategoryCanonical
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
     * @param \Magento\Catalog\Helper\Category $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanUseCanonicalTag(\Magento\Catalog\Helper\Category $subject, $result)
    {
        $currentCategory = $this->_registry->registry('current_category');
        if ($currentCategory && $currentCategory->getData('wp_enable_canonical_url')) {
            return false;
        }
        return $result;
    }
}

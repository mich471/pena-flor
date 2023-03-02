<?php
namespace WeltPixel\NavigationLinks\Plugin;

use WeltPixel\NavigationLinks\Model\Attribute\Source\CategoryLayout;

class CatalogCategoryView
{
    /**
     * @param \Magento\Catalog\Block\Category\View $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsContentMode(\Magento\Catalog\Block\Category\View $subject, $result)
    {
        $category = $subject->getCurrentCategory();
        $subcategoriesLayout = $category->getData('weltpixel_sc_layout');
        if ($subcategoriesLayout == CategoryLayout::LAYOUT_IMAGES) {
            $result = true;
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Block\Category\View $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsMixedMode(\Magento\Catalog\Block\Category\View $subject, $result)
    {
        $category = $subject->getCurrentCategory();
        $subcategoriesLayout = $category->getData('weltpixel_sc_layout');
        if ($subcategoriesLayout == CategoryLayout::LAYOUT_IMAGES) {
            $result = false;
        }
        return $result;
    }
}

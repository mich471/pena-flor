<?php
namespace WeltPixel\NavigationLinks\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use WeltPixel\NavigationLinks\Model\Attribute\Source\CategoryLayout;

class CatalogModelDesign
{
    /**
     * @param \Magento\Catalog\Model\Design $subject
     * @param \Magento\Framework\DataObject $result
     * @param Category|Product $object
     * @return \Magento\Framework\DataObject
     */
    public function afterGetDesignSettings(\Magento\Catalog\Model\Design $subject, $result, $object)
    {
        if ($object instanceof Product) {
            $currentCategory = $object->getCategory();
        } else {
            $currentCategory = $object;
        }

        if (!$currentCategory) {
            return $result;
        }

        $subcategoriesLayout = $currentCategory->getData('weltpixel_sc_layout');
        if ($subcategoriesLayout == CategoryLayout::LAYOUT_IMAGES) {
            $result->setPageLayout('1column');
            $pageLayoutHandles = $result->getPageLayoutHandles();
            $subCategoryLayoutHandle = ['wptype' => 'subcategory'];
            if (is_array($pageLayoutHandles)) {
                $pageLayoutHandles['wptype'] = 'subcategory';
            } else {
                $pageLayoutHandles = $subCategoryLayoutHandle;
            }
            $result->setPageLayoutHandles($pageLayoutHandles);
        }
        return $result;
    }
}

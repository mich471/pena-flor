<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gpf\MegaMenuImagenes\Helper;


use Codazon\MegaMenu\Helper\Category as BaseCategory;

/**
 * Catalog category helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Category extends BaseCategory
{
    const XML_PATH_USE_CATEGORY_CANONICAL_TAG = 'catalog/seo/category_canonical_tag';

    const XML_PATH_CATEGORY_ROOT_ID = 'catalog/category/root_id';

    /**
     * Retrieve category image url
     *
     * @param $categoryNodeId
     * @return string
     */
    public function getCategoryImageUrl($categoryNodeId)
    {
        $categoryId = str_replace('category-node-', '', $categoryNodeId);
        $category = $this->_categoryFactory->create()->load($categoryId);
        return $category->getImageUrl();
    }
}

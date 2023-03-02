<?php
/**
 * Copyright © 2016 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gpf\MegaMenuImagenes\Block\Widget;

use Codazon\MegaMenu\Helper\Category as BaseCategory;
use Gpf\MegaMenuImagenes\Helper\Category;
use Magento\Catalog\Model\Indexer\Category\Flat\State;
use Magento\Catalog\Observer\MenuCategoryData;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Codazon\MegaMenu\Block\Widget\CategoriesTree as BaseCategoriesTree;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\NodeFactory;

/**
 * Html page top menu block
 */
class CategoriesTree extends BaseCategoriesTree implements IdentityInterface
{

    /**
     * CategoriesTree constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param Category $catalogCategory
     * @param BaseCategory $baseCatalogCategory
     * @param State $categoryFlatState
     * @param MenuCategoryData $menuCategoryData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Category $catalogCategory,
        BaseCategory $baseCatalogCategory,
        State $categoryFlatState,
        MenuCategoryData $menuCategoryData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $nodeFactory,
            $treeFactory,
            $baseCatalogCategory,
            $categoryFlatState,
            $menuCategoryData,
            $data
        );
        $this->catalogCategory = $catalogCategory;
    }
    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

		$itemCount = $this->getData('item_count');

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = ($parentLevel === null) ? 1 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $categoryImage = $this->catalogCategory->getCategoryImageUrl($child->getId());
            $html .= '<a class="menu-link" ' .
                ($categoryImage != '' ? 'data-cdz-image="'. $categoryImage .'"' : '') .
                ' href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>' .
                $this->escapeHtml(
                    $child->getName()
                ) .
                '</span></a>' .
                $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) .
                '</li>';
            $itemPosition++;
			if($itemCount){
				if($itemCount == $counter){
					break;
				}
			}
            $counter++;
        }

        return $html;
    }
}

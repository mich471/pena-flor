<?php

namespace WeltPixel\NavigationLinks\Block\Html;

use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use WeltPixel\NavigationLinks\Helper\Data as WpHelper;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * @var BlockRepository
     */
    protected $staticBlockRepository;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var WpHelper
     */
    protected $_wpHelper;

    /**
     * @var string
     */
    protected $inlineStyle = '';

    /**
     * @var string
     */
    protected $inlineDesktopStyle = '';

    /**
     * @var array
     */
    protected $inlineStyleOptions = [];

    /**
     * Topmenu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param BlockRepository $staticBlockRepository
     * @param FilterProvider $filterProvider
     * @param WpHelper $wpHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        BlockRepository $staticBlockRepository,
        FilterProvider $filterProvider,
        WpHelper $wpHelper,
        array $data = []
    ) {
        $this->_filterProvider = $filterProvider;
        $this->staticBlockRepository = $staticBlockRepository;
        $this->_wpHelper = $wpHelper;

        parent::__construct($context, $nodeFactory, $treeFactory, $data);
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        if (!$this->_scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return parent::_getHtml($menuTree, $childrenWrapClass, $limit, $colBrakes);
        }

        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;
        $mobileTreshold = $this->_wpHelper->getWidthThreshold() . 'px';

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /**
         * Mega Menu: check if any child of the current parent has children
         */
        $hasChildrenArr = [];
        foreach ($children as $child) {
            if ($childLevel == 1) {
                if ($child->hasChildren()) {
                    $hasChildrenArr[$child->getParent()->getId()] = true;
                    break;
                }
            }
        }
        /**
         * End Mega Menu
         */

        $liGroup = false;
        $remainingColumnsNumber = 0;

        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }

            /**
             * Mega Menu: settings
             */
            $parent = $child->getParent();
            $hasChildren = '';

            $forceBreak = false;
            $width = '';

            if ($childLevel == 1) {
                if (isset($hasChildrenArr[$parent->getId()])) {
                    $hasChildren = 'data-has-children="1"';
                }

                $columnsNumber = $this->_getColumnsNumber($parent);
                $dynamicSubcategories = (boolean)$parent->getWeltpixelMmDynamicScFlag() && (in_array($parent->getWeltpixelMmDisplayMode(), ['sectioned', 'fullwidth']));
                $columnGroups = explode(",", $parent->getWeltpixelMmDynamicScOpts());
                $columnGroups = array_slice($columnGroups, 0, $columnsNumber);
                $columnGroupsSum = array_sum($columnGroups);
                if ($columnsNumber) {
                    // group items only if the number of subcetegories is bigger than columns numbers value
                    if ($forceBreak || $childrenCount / $columnsNumber < 1) {
                        $liGroup = 1;
                    } else {
                        $liGroup = (int)ceil($childrenCount / $columnsNumber);
                    }

                    if ($dynamicSubcategories) {
                        $liGroup = 0;
                        foreach ($columnGroups as $columnSum) {
                            $liGroup += $columnSum;
                            if ($counter <= $liGroup) {
                                break;
                            }
                        }
                    }

                    if ($remainingColumnsNumber == 0) {
                        $remainingColumnsNumber = $columnsNumber;
                    }
                }

                // force break up ul if remaining children are not enough to fill all the remaining columns
                if (!$forceBreak) {
                    $remainingChildren = ($childrenCount - $counter) + 1;

                    if (!$dynamicSubcategories && $remainingChildren && $columnsNumber && $remainingChildren == $remainingColumnsNumber) {
                        $liGroup = 1;
                        $forceBreak = true;
                    }
                }

                $columnWidth = $this->_getColumnWidth($parent, $columnsNumber);
                $width = $columnWidth ? 'style="width: ' . $columnWidth . '"' : 'style="width: auto"';
                /**
                 * Mega Menu: open columns-group
                 */
                if ($parent->getWeltpixelMmDisplayMode() != 'default' && $counter == 1) {
                    $html .= '<ul class="columns-group starter" ' . $width . '>';
                }
            }
            /**
             * End Mega Menu
             */

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);
            $categLabelText = $child->getData('weltpixel_mm_label_text');
            $categLabelPostion = $child->getData('weltpixel_mm_label_position');
            $designSettingsEnabled = $this->_wpHelper->isDesignSettingsEnabled();
            $mmHoverOption = $this->_wpHelper->getMegaMenuLinksHoverOption();
            $mmHoverTextUnderline = ($designSettingsEnabled && $mmHoverOption ? $this->_wpHelper->getMegaMenuLinksHoverUnderline() : '');

            switch ($mmHoverTextUnderline) {
                case 'bottom-fade-in':
                    $mmHoverTextUnderline = 'bottom-fade-in';
                    break;
                default:
                    $mmHoverTextUnderline = 'underline-megamenu ' . $mmHoverTextUnderline;
                    break;
            }

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            $openInNewTab = '';
            if ($child->getData('open_in_newtab')) {
                $openInNewTab = ' target="_blank" ';
            }

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass
                    . ($categLabelPostion ? ' label-position-' . $categLabelPostion : '') . '"';
                $child->setClass($outermostClass);
            }

            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $forceWidth = $childLevel == 1 && $parent->getWeltpixelMmDisplayMode() == 'sectioned' && $width != '' ? $width : '';

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . ' ' . $hasChildren . ' ' . $forceWidth . ' >';
            if (($childLevel >= 1) && $parent->getData('weltpixel_mm_image_enable')) {
                $categImage = $child->getData('weltpixel_mm_image');
                $categImageAlign = ($parent->getData('weltpixel_mm_image_name_align')) ? $parent->getData('weltpixel_mm_image_name_align') : 'center';
                $categImagePosition = $parent->getData('weltpixel_mm_image_position');
                $categImageAlt = $child->getData('weltpixel_mm_image_alt');
                $categImageContent = '';
                if ($categImage) {
                    $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                    $categImageUrl = str_replace(['media//media', 'media/media'], ['media'], str_replace(['/pub/media'], [''], $mediaUrl) . $categImage);
                    $categImageHeight = $parent->getData('weltpixel_mm_image_height');
                    $categImageWidth = $parent->getData('weltpixel_mm_image_width');
                    $categImageContent = '<span class="mm-image-wrp"><img loading="lazy" src="' . $categImageUrl . '" width="' . $categImageWidth . '" height="' . $categImageHeight . '" alt="' . $categImageAlt . '" /></span>';
                }
                $outermostClassCode = 'class="mm-image mm-align-' . $categImageAlign
                    . ($categLabelPostion ? ' label-position-' . $categLabelPostion : '')
                    . ($categImage ? ($categImagePosition ? ' image-position-' . $categImagePosition : '') : '')
                    . ($childLevel == 1 && $child->getAllChildNodes() ? ' mm-category-title' : '')
                    . '"';
                $html .= '<a' . $openInNewTab . ' href="' . $child->getUrl() . '" ' . $outermostClassCode . '>'
                    . $categImageContent
                    . '<span class="mm-subcategory-title ' . ($mmHoverTextUnderline ? $mmHoverTextUnderline . '"' : '"') . ' > ' . $this->escapeHtml($child->getName())
                    . '</span>' . ($categLabelText ? '<span class="ui-menu-icon megamenu-promo-text megamenu-promo-level'
                    . $childLevel . '-' . $itemPositionClassPrefix . $counter . '">' : '')
                    . ($categLabelText ? $categLabelText . '</span> ' : '')
                    . '</a>' . $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
                    . '</li>';
            } else {
                $html .= '<a' . $openInNewTab . ' href="' . $child->getUrl() . '" '
                    . ($childLevel > 0 ? $outermostClassCode = ' class="' . $outermostClass
                    . ($childLevel == 1 && $child->getAllChildNodes() ? 'mm-category-title' : '')
                    . ($categLabelPostion ? ' label-position-' . $categLabelPostion : '') . ' " ' : $outermostClassCode)
                    . '><span class="mm-subcategory-title ' . ($mmHoverTextUnderline ? $mmHoverTextUnderline . '"' : '"') . ' >'
                    . $this->escapeHtml($child->getName())
                    . '</span>' . ($categLabelText ? '<span class="ui-menu-icon megamenu-promo-text megamenu-promo-level'
                    . $childLevel . '-' . $itemPositionClassPrefix . $counter . '">' : '')
                    . ($categLabelText ? $categLabelText . '</span> ' : '')
                    . '</a>' . $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
                    . '</li>';
            }

            /**
             * Mega Menu: close and re-open columns-group
             */
            if ($parent->getWeltpixelMmDisplayMode() != 'default' && $liGroup && $childLevel == 1) {
                if ($dynamicSubcategories && ($liGroup == $columnGroupsSum)) {
                    $remainingColumnsNumber--;
                } elseif ($liGroup == 1 && $counter != $childrenCount) {
                    $html .= '</ul>';
                    $html .= '<ul class="columns-group inner" ' . $width . '>';
                    $remainingColumnsNumber--;
                } else {
                    if (
                        ($counter % $liGroup == 0 && $counter > 1 && $counter != $childrenCount) ||
                        ($forceBreak && $counter != $childrenCount)
                    ) {
                        $html .= '</ul>';
                        $html .= '<ul class="columns-group inner"' . $width . '>';
                        $remainingColumnsNumber--;
                    }
                }
            }

            /**
             * Mega Menu: close columns-group
             */
            if ($childLevel == 1 && $parent->getWeltpixelMmDisplayMode() != 'default' && $counter == $childrenCount) {
                $html .= '<span class="close columns-group last"></span>';
                $html .= '</ul>';
            }
            $categImageRadius = $parent->getData('weltpixel_mm_image_radius');

            if (strlen($categImageRadius)) {
                $this->inlineStyle .= 'body .nav-sections .navigation ul li.megamenu.level0.' . $parentPositionClass  . ' ul.submenu li a span.mm-image-wrp img'
                    . '{ border-radius:' . $categImageRadius . '; }';
            }

            $categoryFontColor = $child->getWeltpixelMmFontColor();
            $categoryFontHoverColor = $child->getWeltpixelMmFontHoverColor();
            $categoryFontTextShadow = false;
            $categLabelTextColor = $child->getData('weltpixel_mm_label_font_color');
            $categLabelBackgorundColor = $child->getData('weltpixel_mm_label_background_color');

            if (strlen($categoryFontColor)) {
                $categoryFontTextShadow = $categoryFontColor;
                $this->inlineStyle .= 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ' > a span:nth-child(2) ,' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ' > a.bold-menu span:nth-child(2),' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ' > a.bold-menu:visited span:nth-child(2),' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ' > a:visited span:nth-child(2) { color: ' . $categoryFontColor . ' !important;}';
            }
            if (strlen($categoryFontHoverColor)) {
                $categoryFontTextShadow = $categoryFontHoverColor;
                $this->inlineStyle .= 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ':hover > a span, ' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ' > a:hover span { color: ' . $categoryFontHoverColor . ' !important;}' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ':hover > a:hover span { color: ' . $categoryFontHoverColor . ' !important;  text-shadow: 0 0 0 ' . $categoryFontHoverColor . ' !important;}' .
                    'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ':hover > a span { text-shadow: 0 0 0 ' . $categoryFontHoverColor . ' !important;}';
            }
            if ($categoryFontTextShadow) {
                $this->inlineDesktopStyle .= 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel . '.' . $itemPositionClassPrefix . $counter . ':hover > a { text-shadow: 0 0 0 ' . $categoryFontTextShadow . ' !important;}';
            }

            if (strlen($categLabelTextColor)) {
                $this->inlineStyle .= 'body .nav-sections .navigation ul li.megamenu.level'
                    . $childLevel . '.' . $itemPositionClassPrefix . $counter
                    . ' .megamenu-promo-level' . $childLevel . '-' . $itemPositionClassPrefix . $counter
                    . '{ color: ' . $categLabelTextColor . ' !important ;}';
            }

            if (strlen($categLabelBackgorundColor)) {
                $this->inlineStyle .= 'body .nav-sections .navigation ul li.megamenu.level'
                    . $childLevel . '.' . $itemPositionClassPrefix . $counter
                    . ' .megamenu-promo-level' . $childLevel . '-' . $itemPositionClassPrefix . $counter
                    . ' { background: ' . $categLabelBackgorundColor . '; }';
            }

            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul>';
        }

        if (($childLevel == 0) && (strlen($this->inlineStyle) || strlen($this->inlineDesktopStyle))) {
            $html .= "<style>";
            if (strlen($this->inlineStyle)) {
                $html .= $this->inlineStyle;
            }
            if (strlen($this->inlineDesktopStyle)) {
                $html .= '@media (min-width: ' . $mobileTreshold . ') {';
                $html .= $this->inlineDesktopStyle;
                $html .= '}';
            }
            $html .= "</style>";
        }

        return $html;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        if (!$this->_scopeConfig->getValue(
            'weltpixel_megamenu/megamenu/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return parent::_addSubMenu($child, $childLevel, $childrenWrapClass, $limit);
        }
        $html = '';

        /**
         * Mega Menu: settings
         */
        $megaMenuClass = '';
        $designSettingsEnabled = $this->_wpHelper->isDesignSettingsEnabled();

        $backgroundColor = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuBackgroundColor() : '');
        $mmFontColor = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuFontColor() : '');
        $mmHoverOption = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuLinksHoverOption() : '');
        $mmHoverFontColor = ($designSettingsEnabled && $mmHoverOption ? $this->_wpHelper->getMegaMenuLinksHoverColor() : '');
        $mmHoverTextUnderline = ($designSettingsEnabled && $mmHoverOption ? $this->_wpHelper->getMegaMenuLinksHoverUnderline() : '');
        $continuityTopLevelWithMegaMenu = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuContinuity() : '');
        $mmSubMenuPadding = ($designSettingsEnabled ? $this->_wpHelper->getSubMenuPadding() : '');
        $mmSubMenuTextTransformOption = ($designSettingsEnabled ? $this->_wpHelper->isTextTransformEnabled() : '');
        $mmSubMenuFontSizeL0 = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuFontSizeL0() : '');
        $mmSubMenuFontSizeL1 = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuFontSizeL1() : '');
        $mmSubMenuFontSizeL2 = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuFontSizeL2() : '');
        $mmItemsPaddingL0 = ($designSettingsEnabled ? $this->_wpHelper->getMegaMenuLinksPaddingLevel0() : '');

        $mmSubMenuFontSizeL0 = strlen(trim($mmSubMenuFontSizeL0)) ? 'font-size:' . $mmSubMenuFontSizeL0 : 'font-size: inherit;';
        $mmSubMenuFontSizeL1 = strlen(trim($mmSubMenuFontSizeL1)) ? 'font-size:' . $mmSubMenuFontSizeL1 : 'font-size: inherit;';
        $mmSubMenuFontSizeL2 = strlen(trim($mmSubMenuFontSizeL2)) ? 'font-size:' . $mmSubMenuFontSizeL2 : 'font-size: inherit;';

        if ($childLevel == 0) {
            $megaMenuClass = $child->getWeltpixelMmDisplayMode() ? $child->getWeltpixelMmDisplayMode() : 'sectioned';
            $megaMenuClass .= $child->getWeltpixelMmMobHideAllcat() ? ' hide-all-category' : '';
        }
        /**
         * End Mega Menu
         */

        /**
         * Get Menu Blocks
         */
        $menuBlocks = [
            'weltpixel_mm_top_block' => false,
            'weltpixel_mm_right_block' => false,
            'weltpixel_mm_bottom_block' => false,
            'weltpixel_mm_left_block' => false,
        ];
        $submenuClass = '';
        $submenuChildClass = '';
        $blocks = [];
        $childHasChildren = $child->hasChildren();
        $menuBlocksNoChildClass = $childHasChildren ? '' : 'no-child';
        if ($childLevel == 0) {
            $blocks = [
                'weltpixel_mm_top_block',
                'weltpixel_mm_right_block',
                'weltpixel_mm_bottom_block',
                'weltpixel_mm_left_block',
            ];
            $menuBlocks = $this->_getMenuBlock($blocks, $child);
            $submenuClass = $this->_getSubmenuClass($menuBlocks);
            $submenuChildClass = $this->_getSubmenuChildClass($menuBlocks);
        }

        if (!$childHasChildren && !$this->_areMenuBlocksUsed($menuBlocks)) {
            return $html;
        }
        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        // columns settings
        $columnsNumber = $this->_getColumnsNumber($child);
        $columnWidth = $this->_getColumnWidth($child, $columnsNumber);
        $width = $columnWidth ? 'style="width: ' . $columnWidth . '"' : 'style="width: auto"';

        $html .= '<ul class="level' . $childLevel . ' submenu ' . $megaMenuClass . ' '
            . $submenuClass . ' ' . $submenuChildClass . '" style="display: none;' . '">';

        if (strlen($backgroundColor)) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . ($megaMenuClass === 'fullwidth' ? $megaMenuClass . ' > li div.fullwidth-wrapper' : $megaMenuClass)
                . ',' . 'body .nav-sections .navigation .megamenu .submenu'
                . '{ background: ' . $backgroundColor . ' ;}';
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li a'
                . '{ background: ' . $backgroundColor . ' ;}';
        }

        if (strlen($mmFontColor)) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation .level' . $childLevel . '.submenu a, '
                . '.navigation .megamenu.level0 .submenu .active > a, '
                . '.navigation .megamenu.level0 .submenu .has-active > a'
                . '{ color:' . $mmFontColor . ' ;}';

            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level-top-fullwidth'
                . '.level' . $childLevel . ' ul.level' . $childLevel . '.submenu [data-has-children]' . ' > '
                . ' a span:nth-last-child(2):before' . ','
                . 'body .nav-sections .navigation ul li.megamenu.level-top-sectioned'
                . '.level' . $childLevel . ' ul.level' . $childLevel . '.submenu [data-has-children]' . ' > '
                . ' a span:nth-last-child(2):before'
                . '{ background-color:' . $mmFontColor . ' ;}';
        }

        if ($mmHoverTextUnderline) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li a:hover > span:before,'
                . 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li a:hover > span.' . $mmHoverTextUnderline . ':after'
                . '{ background-color: ' . ($mmHoverFontColor ? $mmHoverFontColor : '#ccc') . ' !important;}';
        }

        if ($mmHoverFontColor) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li a:hover'
                . '{ color:' . $mmHoverFontColor . ' !important;}';
        }
        $mmHoverFontColorUiState = ($mmHoverFontColor) ? $mmHoverFontColor : '#000';
        $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
            . ' ul.level' . $childLevel . '.submenu' . '.'
            . $megaMenuClass . ' li a.ui-state-focus'
            . '{ color:' . $mmHoverFontColorUiState . ' !important;}';

        if ($continuityTopLevelWithMegaMenu) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level0.mm-has-children'
                . ':hover > a { background:' . ($backgroundColor ? $backgroundColor : 'transparent') . ' ;}';
        } else {
            $this->inlineStyleOptions[] = 'body .page-wrapper .nav-sections .navigation ul li.megamenu.mm-first-item a.level-top'
                . '{ padding-left: 0px ;}';
        }

        if (strlen($mmSubMenuPadding)) {
            $this->inlineStyleOptions[] = '.nav-sections:not(.nav-mobile) .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li.mm-no-children '
                . '{ padding:' . $mmSubMenuPadding . ' ;}';

            $mmSubMenuPaddingArray = explode(' ', $mmSubMenuPadding);
            switch (count($mmSubMenuPaddingArray)) {
                case 1:
                    $mmSubMenuPaddingArray[1] = intval($mmSubMenuPaddingArray[0]) + 10 . 'px';
                    break;
                case 2:
                case 3:
                    $mmSubMenuPaddingArray[1] = intval($mmSubMenuPaddingArray[1]) + 10 . 'px';
                    break;
                case 4:
                    $mmSubMenuPaddingArray[1] = intval($mmSubMenuPaddingArray[1]) + 10 . 'px';
                    $mmSubMenuPaddingArray[3] = intval($mmSubMenuPaddingArray[3]) + 10 . 'px';
                    break;
            }

            $mmSubMenuPadding = implode(' ', $mmSubMenuPaddingArray);
            $this->inlineStyleOptions[] = '.nav-sections:not(.nav-mobile) .nav-sections .navigation ul li.level0 .parent > a.mm-category-title'
                . '{ padding:' . $mmSubMenuPadding . ' ;}';
        }

        if (strlen($mmSubMenuTextTransformOption)) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level' . $childLevel
                . ' ul.level' . $childLevel . '.submenu' . '.'
                . $megaMenuClass . ' li a span.mm-subcategory-title'
                . '{ text-transform:' . ($mmSubMenuTextTransformOption ? $mmSubMenuTextTransformOption : 'none') . ' ;}';

            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level-top-fullwidth'
                . '.level' . $childLevel . ' ul.level' . $childLevel . '.submenu [data-has-children]' . ' > '
                . ' a span.mm-subcategory-title' . ','
                . 'body .nav-sections .navigation ul li.megamenu.level-top-sectioned'
                . '.level' . $childLevel . ' ul.level' . $childLevel . '.submenu [data-has-children]' . ' > '
                . ' a span.mm-subcategory-title'
                . '{ text-transform: uppercase ;}';
        }

        if (strlen($mmSubMenuFontSizeL0)) {
            $this->inlineStyleOptions[] = '.nav-sections:not(.nav-mobile) .navigation ul li.level0 > a span:first-child,'
                . '.nav-sections:not(.nav-mobile) .navigation ul li.level0 > a span:nth-child(2)'
                . '{ ' . $mmSubMenuFontSizeL0 . ';}';
        }
        if (strlen($mmSubMenuFontSizeL1)) {
            $this->inlineStyleOptions[] =  '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-fullwidth .submenu .columns-group li.level1 > a span,'
                . '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-sectioned .submenu .columns-group li.level1 > a span,'
                . '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-boxed .submenu .columns-group li.level1 > a span'
                . '{ ' . $mmSubMenuFontSizeL1 . ';}';
        }
        if (strlen($mmSubMenuFontSizeL2)) {
            $this->inlineStyleOptions[] = '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-fullwidth .submenu .columns-group li.level2 > a span,'
                . '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-sectioned .submenu .columns-group li.level2 > a span,'
                . '.nav-sections:not(.nav-mobile) .navigation .megamenu.level-top-boxed .submenu .columns-group li.level2 > a span'
                . '{ ' . $mmSubMenuFontSizeL2 . ';}';
        }
        if (strlen($mmItemsPaddingL0)) {
            $this->inlineStyleOptions[] = 'body .nav-sections .navigation ul li.megamenu.level0 a.level-top'
                . '{ padding-left:' . $mmItemsPaddingL0 . ';'
                . 'padding-right:' . $mmItemsPaddingL0 . ' ;}';
        }

        if ($childLevel == 0) {
            $html .= '<li class="submenu-child">';
        }

        if ($childLevel == 0 && strpos($megaMenuClass, 'fullwidth') !== false) {
            $html .= '<div class="fullwidth-wrapper">';
            $html .= '<div class="fullwidth-wrapper-inner">';
        }

        /**
         * Insert top and left blocks
         */
        if ($child->getWeltpixelMmDisplayMode() != 'default') {
            $blockCode = 'weltpixel_mm_top_block';
            if ($menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block top-group inner">';
                    $html .= '<li class="submenu-child top-block-child">';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' top-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' top-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                }

                if (strpos($megaMenuClass, 'fullwidth') === false && $megaMenuClass != 'default') {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child"><!-- re-open submenu-child -->';
                }
            }

            $blockCode = 'weltpixel_mm_left_block';
            if ($menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block left-group inner" ' . $width . '>';
                    $html .= '<li class="submenu-child left-block-child">';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' left-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' left-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                }

                if (strpos($megaMenuClass, 'fullwidth') === false && $megaMenuClass != 'default') {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child"><!-- re-open submenu-child -->';
                }
            }
        }

        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);

        /**
         * Insert right and bottom blocks
         */
        if ($child->getWeltpixelMmDisplayMode() != 'default') {
            $blockCode = 'weltpixel_mm_right_block';
            if ($menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block right-group inner" ' . $width . '>';
                    $html .= '<li class="submenu-child right-block-child">';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' right-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child right-block-child"><!-- re-open submenu-child -->';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' right-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li><!-- close submenu-child -->';
                }
            }

            $blockCode = 'weltpixel_mm_bottom_block';
            if ($menuBlocks[$blockCode] !== false) {
                if (strpos($megaMenuClass, 'fullwidth') !== false) {
                    $html .= '<ul class="columns-group columns-group-block bottom-group inner">';
                    $html .= '<li class="submenu-child bottom-block-child">';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' bottom-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';
                } else {
                    $html .= '</li><!-- close submenu-child -->';
                    $html .= '<li class="submenu-child bottom-block-child"><!-- re-open submenu-child -->';
                    $html .= '<div class="menu-block ' . $menuBlocksNoChildClass . ' bottom-block block-container">' . $menuBlocks[$blockCode] . '</div>';
                    $html .= '</li><!-- close submenu-child -->';
                }
            }
        }

        if ($childLevel == 0 && strpos($megaMenuClass, 'fullwidth') !== false) {
            $html .= '</div>';
            $html .= '</div>';
        }

        if ($childLevel == 0) {
            $html .= '</li><!-- end submenu-child -->';
        }

        $html .= '</ul><!-- end submenu -->';

        return $html;
    }

    /**
     * Returns array of menu item's classes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(\Magento\Framework\Data\Tree\Node $item)
    {
        $classes = [];

        /**
         * Mega Menu: settings
         */
        $classes[] = 'megamenu';

        if ($item->getLevel() == 0) {
            $displayMode = $item->getWeltpixelMmDisplayMode() ? $item->getWeltpixelMmDisplayMode() : 'sectioned';
            $classes[] = $item->getClass() . '-' . $displayMode;
            $item->getUrl() == $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) ?
                $classes[] = 'active' :
                $classes[] = '';

            if (($displayMode == 'default') && $item->getWeltpixelMmShowArrows()) {
                $classes[] = 'show-arrows';
            }
        }
        /**
         * End Mega Menu
         */

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsCategory()) {
            $classes[] = 'category-item';
        }

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        } elseif ($item->getHasActive()) {
            $classes[] = 'has-active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        return $classes;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Theme\Block\Html\Topmenu'));
        return parent::_toHtml();
    }

    /**
     * Get columns number width from category config
     *
     * @param $item
     * @return string
     */
    protected function _getColumnsNumber($item)
    {
        if ($item->getWeltpixelMmDisplayMode() == 'boxed') {
            $columnsNumber = '1';
        } else {
            $columnsNumber = $item->getWeltpixelMmColumnsNumber() ? trim($item->getWeltpixelMmColumnsNumber()) : '4';
        }

        return $columnsNumber;
    }

    /**
     * Get column width from category config
     *
     * @param $item
     * @param $columnsNumber
     * @return bool|string
     */
    protected function _getColumnWidth($item, $columnsNumber)
    {
        $numbers = (float)preg_replace('/[^0-9.]*/', '', trim($item->getWeltpixelMmColumnWidth()));
        $characters = preg_replace('/[^a-zA-Z%]/', '', trim($item->getWeltpixelMmColumnWidth()));

        switch ($item->getWeltpixelMmDisplayMode()) {
            case 'fullwidth':
                switch (trim(strtolower($characters))) {
                    case '%':
                        $columnWidth = (float)$numbers . '%';
                        break;
                    case 'px':
                        $columnWidth = (int)$numbers . 'px';
                        break;
                    default:
                        $columnWidth = (float)100 / $columnsNumber . '%';
                }
                break;
            case 'sectioned':
                $columnWidth = 'auto';
                break;
            case 'boxed':
                $columnWidth = false;
                break;
            default:
                $columnWidth = false;
                break;
        }

        return $columnWidth;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(\Magento\Framework\Data\Tree\Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);

        if (strpos($item->getUrl(), 'javascript:void') !== false && isset($attributes['class'])) {
            $attributes['class'] .= ' disabled-link';
        }
        ($item->getData('is_first') ? $attributes['class'] .= ' mm-first-item' : $attributes['class'] .= '');
        ($item->getAllChildNodes() ? $attributes['class'] .= ' mm-has-children' : $attributes['class'] .= ' mm-no-children');

        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }
        return $html;
    }

    /**
     * @param $blocks
     * @param $child
     * @return array
     */
    protected function _getMenuBlock($blocks, $child)
    {
        $menuBlocks = [];
        foreach ($blocks as $block) {
            // continue if no block is set
            if ($child->getWeltpixelMmDisplayMode() == 'default' || $child->getData($block . '_type') == 'none') {
                $menuBlocks[$block] = false;
                continue;
            }

            $content = '';
            if ($child->getData($block . '_type') == 'block_cms') {
                try {
                    $cmsBlock = $this->staticBlockRepository->getById($child->getData($block . '_cms'));
                    if ($cmsBlock && $cmsBlock->getIsActive()) {
                        $content = $cmsBlock->getContent();
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
                    $content = '';
                }
            } else {
                $content = $child->getData($block);
            }

            if ($content != '') {
                $this->templateEnginePool;
                $menuBlocks[$block] = $this->_filterProvider->getPageFilter()->filter($content);
            } else {
                $menuBlocks[$block] = false;
            }
        }

        return $menuBlocks;
    }

    /**
     * @param $menuBlocks
     * @return string
     */
    protected function _getSubmenuClass($menuBlocks)
    {
        $class = '';
        foreach ($menuBlocks as $code => $content) {
            if ($content) {
                $class .= ' has-menu-block';
                break;
            }
        }

        return $class;
    }

    /**
     * @param $menuBlocks
     * @return string
     */
    protected function _getSubmenuChildClass($menuBlocks)
    {
        $class = '';
        foreach ($menuBlocks as $code => $content) {
            $codeArr = explode('_', $code);
            if ($content) {
                $class .= $codeArr[2] . '-block-child ';
            }
        }

        return $class;
    }

    /**
     * @param array $blocks
     * @return bool
     */
    protected function _areMenuBlocksUsed(array $blocks)
    {
        foreach ($blocks as $blockContent) {
            if (strlen(trim($blockContent))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_before',
            ['menu' => $this->getMenu(), 'block' => $this, 'request' => $this->getRequest()]
        );

        $this->getMenu()->setOutermostClass($outermostClass);
        $this->getMenu()->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->getMenu(), $childrenWrapClass, $limit);
        if (count($this->inlineStyleOptions)) {
            $this->inlineStyleOptions = array_unique($this->inlineStyleOptions);
            $html .= "<style>";
            $html .= implode($this->inlineStyleOptions);
            $html .= "</style>";
        }

        $transportObject = new DataObject(['html' => $html]);
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_after',
            ['menu' => $this->getMenu(), 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();
        return $html;
    }
}

<?php

namespace WeltPixel\SmartProductTabs\Plugin\Tabs;

use Magento\Framework\App\RequestInterface;
use WeltPixel\SmartProductTabs\Helper\Data as SmartProductTabsHelper;
use WeltPixel\SmartProductTabs\Model\SmartProductTabsBuilder;

class Details
{
    /**
     * @var SmartProductTabsBuilder
     */
    protected $smartProductTabsBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SmartProductTabsHelper
     */
    protected $smartProductTabsHelper;

    /**
     * Details constructor.
     * @param SmartProductTabsBuilder $smartProductTabsBuilder
     * @param SmartProductTabsHelper $smartProductTabsHelper
     * @param RequestInterface $request
     */
    public function __construct(
        SmartProductTabsBuilder $smartProductTabsBuilder,
        SmartProductTabsHelper $smartProductTabsHelper,
        RequestInterface $request
    ) {
        $this->smartProductTabsBuilder = $smartProductTabsBuilder;
        $this->smartProductTabsHelper = $smartProductTabsHelper;
        $this->request = $request;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Details $subject
     * @param array $result
     * @return array
     */
    public function afterGetGroupSortedChildNames(
        \Magento\Catalog\Block\Product\View\Details $subject,
        $result
    ) {
        if ($this->smartProductTabsHelper->isSmartProductTabsEnabled()) {
            $layout = $subject->getLayout();
            $productId = $this->request->getParam('id');
            $smartTabs = $this->smartProductTabsBuilder->getSmartProductTabsForProduct($productId);

            $childNamesSortOrder = [];

            foreach ($result as $systemTabName) {
                $systemTabBlock = $layout->getBlock($systemTabName);
                $sortOrder = (int)$systemTabBlock->getData('sort_order') ?? 0;

                $systemTabOptions = $this->smartProductTabsHelper->getSystemTabOptions($systemTabName);
                if ($systemTabOptions['changes_enabled']) {
                    if (!$systemTabOptions['show']) {
                        continue;
                    }
                    if ($systemTabOptions['change_sort_order']) {
                        $sortOrder = $systemTabOptions['sort_order'];
                    }
                    if ($systemTabOptions['change_title']) {
                        $systemBlock = $layout->getBlock($systemTabName);
                        $systemBlock->setTitle($systemTabOptions['title']);
                    }
                }
                $childNamesSortOrder[$systemTabName] = $sortOrder;
            }

            foreach ($smartTabs as $tab) {
                $childNamesSortOrder['product.info.details.wp.' . $tab['id']] = $tab['position'];
            }

            asort($childNamesSortOrder, SORT_NUMERIC);
            $result = array_keys($childNamesSortOrder);
        }
        return $result;
    }
}

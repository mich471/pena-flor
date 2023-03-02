<?php

namespace WeltPixel\SmartProductTabs\Observer;

use     Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use WeltPixel\SmartProductTabs\Helper\Data as SmartProductTabsHelper;
use WeltPixel\SmartProductTabs\Model\SmartProductTabsBuilder;

class AddSmartTabs implements ObserverInterface
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
     * AddSmartTabs constructor.
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
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->smartProductTabsHelper->isSmartProductTabsEnabled()) {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $observer->getLayout();
            $productInfoDetailsBlock = $layout->getBlock('product.info.details');
            if ($productInfoDetailsBlock) {
                $productId = $this->request->getParam('id');
                $smartTabs = $this->smartProductTabsBuilder->getSmartProductTabsForProduct($productId);

                foreach ($smartTabs as $tab) {
                    $productInfoDetailsBlock->addChild(
                        'wp.' . $tab['id'],
                        \WeltPixel\SmartProductTabs\Block\DynamicSmartProductTabs::class,
                        [
                            'template' => '',
                            'title'     =>  $tab['title'],
                            'content' => $tab['content']
                        ]
                    );
                }
            }
        }
        return $this;
    }
}

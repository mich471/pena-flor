<?php
namespace WeltPixel\SmartProductTabs\Block\Adminhtml\SmartProductTabs\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \WeltPixel\SmartProductTabs\Model\SmartProductTabsFactory
     */
    protected $smartProductTabsFactory;

    /**
     * @param Context $context
     * @param \WeltPixel\SmartProductTabs\Model\SmartProductTabsFactory $smartProductTabsFactory
     */
    public function __construct(
        Context $context,
        \WeltPixel\SmartProductTabs\Model\SmartProductTabsFactory $smartProductTabsFactory
    ) {
        $this->context = $context;
        $this->smartProductTabsFactory = $smartProductTabsFactory;
    }

    /**
     * Return item ID
     *
     * @return int|null
     */
    public function getSmartProductTabId()
    {
        try {
            /** @var \WeltPixel\SmartProductTabs\Model\SmartProductTabs $smartProductTab */
            $smartProductTab = $this->smartProductTabsFactory->create();
            return $smartProductTab->load($this->context->getRequest()->getParam('id'))->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}

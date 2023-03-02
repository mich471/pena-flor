<?php

namespace WeltPixel\ProductPage\Controller\Visitor;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Push
 * @package WeltPixel\ProductPage\Controller\Visitor
 */
class Push extends Action
{

    /**
     * @var \WeltPixel\ProductPage\Helper\Data
     */
    protected $wpHelper;

    /**
     * @var \WeltPixel\ProductPage\Model\VisitorCounterManager
     */
    protected $visitorCounterManager;

    /**
     * Push constructor.
     * @param Context $context
     * @param \WeltPixel\ProductPage\Helper\Data $wpHelper
     * @param \WeltPixel\ProductPage\Model\VisitorCounterManager $visitorCounterManager
     */
    public function __construct(
        Context $context,
        \WeltPixel\ProductPage\Helper\Data $wpHelper,
        \WeltPixel\ProductPage\Model\VisitorCounterManager $visitorCounterManager
    ) {
        parent::__construct($context);
        $this->wpHelper = $wpHelper;
        $this->visitorCounterManager = $visitorCounterManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->wpHelper->isVisitorCounterEnabled()) {
            return $this->prepareResult([
                'result' => false
            ]);
        }
        $productId = $this->getRequest()->getParam('product_id');
        if (!$productId) {
            return $this->prepareResult([
                'result' => false
            ]);
        }

        $intervalCheck = $this->wpHelper->getVisitorCounterIntervalCheck();
        $updateFrequency = $this->wpHelper->getVisitorCounterUpdateFrequency();
        if ($updateFrequency == \WeltPixel\ProductPage\Model\Config\Source\VisitorCounterUpdateFrequency::UPDATE_PAGEREFRESH) {
            $intervalCheck = $this->wpHelper->getVisitorCounterRefreshDelay();
        }

        $result = $this->visitorCounterManager->updateCounter($productId, $intervalCheck);
        if (!$result) {
            return $this->prepareResult([
                'result' => false
            ]);
        }

        return $this->prepareResult([
            'result' => true,
            'counter' => $result
        ]);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}

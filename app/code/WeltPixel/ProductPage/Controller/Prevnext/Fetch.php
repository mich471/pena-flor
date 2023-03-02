<?php

namespace WeltPixel\ProductPage\Controller\Prevnext;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Prevnext
 * @package WeltPixel\ProductPage\Controller\Prevnext
 */
class Fetch extends Action
{

    /**
     * @var \WeltPixel\ProductPage\Helper\Data
     */
    protected $wpHelper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Fetch constructor.
     * @param Context $context
     * @param \WeltPixel\ProductPage\Helper\Data $wpHelper
     * @param ProductRepository $productRepository
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \WeltPixel\ProductPage\Helper\Data $wpHelper,
        ProductRepository $productRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->wpHelper = $wpHelper;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->wpHelper->isPrevNextEnabled()) {
            $result->setData(['result' => false]);
            return $result;
        }
        $productIds = $this->getRequest()->getParam('productIds');
        if (!$productIds) {
            $result->setData(['result' => false]);
            return $result;
        }

        $productDetails = [];
        foreach ($productIds as $productId) {
            if ($productId) {
                try {
                    $product = $this->productRepository->getById($productId);
                    $productDetails[$productId] = [
                        'html' => $product->getName(),
                        'href' => $product->getProductUrl()
                    ];
                } catch (\Exception $ex) {}
            }
        }

        $result->setData([
            'result' => true,
            'productInfo' => $productDetails
        ]);
        return $result;
    }
}

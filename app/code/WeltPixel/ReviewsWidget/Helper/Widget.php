<?php

namespace WeltPixel\ReviewsWidget\Helper;

use Magento\Catalog\Model\Product;

/**
 * Class Widget
 * @package WeltPixel\ReviewsWidget\Helper
 */
class Widget extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Get product info
     *
     * @return Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductInfo($productId)
    {
        return $this->productRepository->getById(
            $productId,
            false,
            $this->storeManager->getStore()->getId()
        );
    }
}

<?php

namespace Gpf\Catalog\Plugin\Block\Catalog\Product\View\Type;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle as ParentBundle;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Json\DecoderInterface as JsonDecoderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException as Exception;

class Bundle
{

    const DEFAULT_VALUE = "NO";
    /**
     * @var JsonEncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var JsonDecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Bundle constructor.
     * @param JsonEncoderInterface $jsonEncoder
     * @param JsonDecoderInterface $jsonDecoder
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        JsonEncoderInterface $jsonEncoder,
        JsonDecoderInterface $jsonDecoder,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->productRepository = $productRepository;
    }

    public function afterGetJsonConfig(ParentBundle $bundle, $result)
    {
        $result = $this->jsonDecoder->decode($result);
        foreach ($result['options'] as &$option) {
            foreach ($option['selections'] as &$selection) {
                try {
                    $product = $this->productRepository->getById($selection['optionId']);
                } catch (Exception $exception) {
                    continue;
                }
                $varietal = $product
                    ->getResource()
                    ->getAttribute('varietal')
                    ->getFrontend()
                    ->getValue($product);
                if (strtoupper($varietal) != self::DEFAULT_VALUE) {
                    $selection['name'] = $product->getName() . ' - ' . $varietal;
                }
            }
        }

        return $this->jsonEncoder->encode($result);
    }

}
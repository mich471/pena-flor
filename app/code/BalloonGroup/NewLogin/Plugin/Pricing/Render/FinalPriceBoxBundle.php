<?php

namespace BalloonGroup\NewLogin\Plugin\Pricing\Render;

use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class FinalPriceBoxBundle extends \Magento\Bundle\Pricing\Render\FinalPriceBox
{

    const VENTA_INSTITUCIONAL_CODE = 'ventainstitucional';
    /**
     * @var Context
     */
    protected $httpContext;
    /**
     * @var StoreManagerInterface
     */
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        \Magento\Framework\Pricing\Price\PriceInterface $price,
        \Magento\Framework\Pricing\Render\RendererPool $rendererPool,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [],
        \Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface $salableResolver = null,
        \Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
    	$this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        parent::__construct($context,
                            $saleableItem,
                            $price,
                            $rendererPool,
                            $data,
                            $salableResolver,
                            $minimalPriceCalculator);
    }

    /**
     * @param $html
     * @return string
     * @throws NoSuchEntityException
     */
    protected function wrapResult($html): string
    {
        $storeCode = $this->storeManager->getStore()->getCode();
        $isLoggedIn =    $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if(self::VENTA_INSTITUCIONAL_CODE == $storeCode && !$isLoggedIn){
            $wording = __('Please Login To See Price');
            return '<div class="" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                '>'.$wording.'</div>';
        }else{
            return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
                    'data-role="priceBox" ' .
                    'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                    '>' . $html . '</div>';
        }
    }
}

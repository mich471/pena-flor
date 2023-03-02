<?php

namespace WeltPixel\GoogleCards\Block\Configurable;

use Magento\Catalog\Api\Data\ProductInterface;

class GoogleCards extends \WeltPixel\GoogleCards\Block\GoogleCards
{
    /**
     * GoogleCards constructor.
     * @param \Magento\Catalog\Block\Product\Context $productContext
     * @param \WeltPixel\GoogleCards\Helper\Data $helper
     * @param \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory
     * @param CollectionFactory $_reviewsFactory
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $productContext,
        \WeltPixel\GoogleCards\Helper\Data $helper,
        \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $_reviewsFactory,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($productContext, $helper, $reviewSummaryFactory, $_reviewsFactory, $logo, $context, $data);
    }

    /**
     * @return ProductInterface[]
     */
    public function getChildProducts()
    {
        $product = $this->getProduct();
        $childProducts = $product->getTypeInstance()->getUsedProducts($product);
        return $childProducts;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $isGoogleCardsConfigurableSimpleSeparately = $this->_helper->isGoogleCardsConfigurableSimpleSeparately();
        $templateFile = parent::getTemplate();
        if (!$isGoogleCardsConfigurableSimpleSeparately) {
            $templateFile = 'WeltPixel_GoogleCards::meta_head.phtml';
        }
        return $templateFile;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getPriceForSimpleProduct($product)
    {
        $priceInfo = $product->getPriceInfo()->getPrice('final_price')->getAmount();
        $price = $priceInfo->getValue();

        $priceOption = $this->_helper->getGoogleCardsPrice();

        if ((int)$this->_scopeConfig->getValue(
            'tax/display/type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) === 3
        ) {
            switch ($priceOption) {
                case 'incl_tax':
                    $price = $priceInfo->getValue();
                    break;
                case 'excl_tax':
                    $price = $priceInfo->getValue('tax');
                    break;
            }
        }

        return number_format($price, 2, '.', '');
    }
}

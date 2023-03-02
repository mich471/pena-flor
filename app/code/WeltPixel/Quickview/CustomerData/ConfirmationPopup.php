<?php
namespace WeltPixel\Quickview\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * Gtm section
 */
class ConfirmationPopup extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * \WeltPixel\Quickview\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * Constructor
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \WeltPixel\Quickview\Helper\Data $_wpHelper
     * @param \Magento\Framework\View\Element\BlockFactory $_blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $_checkoutSession,
        \WeltPixel\Quickview\Helper\Data $_wpHelper,
        \Magento\Framework\View\Element\BlockFactory $_blockFactory,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_checkoutSession = $_checkoutSession;
        $this->_wpHelper = $_wpHelper;
        $this->_blockFactory = $_blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->_wpHelper->isAjaxCartEnabled()) {
            return [];
        }
        $productId =  $this->_checkoutSession->getLastAddedProductId();
        $abstractProductBlock = $this->_blockFactory->createBlock('\Magento\Catalog\Block\Product\AbstractProduct');
        $confirmationPopupBlock = $this->_blockFactory->createBlock('\WeltPixel\Quickview\Block\ConfirmationPopup')
            ->setTemplate('WeltPixel_Quickview::confirmation_popup/content.phtml')
            ->setProductViewModel($abstractProductBlock)
            ->setLastAddedProductId($productId);

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $confirmationPopupBlock->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $confirmationPopupBlock->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $confirmationPopup = $confirmationPopupBlock->toHtml();

        return [
            'confirmation_popup_content' => $confirmationPopup
        ];
    }
}

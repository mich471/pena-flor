<?php
namespace WeltPixel\Quickview\Plugin\Tax;

class PopupPrice extends \Magento\Tax\Plugin\Checkout\CustomerData\Cart
{
    /**
     * @param \WeltPixel\Quickview\Block\ConfirmationPopup $subject
     * @param $result
     * @return array
     */
    public function afterGetPopupDetails(\WeltPixel\Quickview\Block\ConfirmationPopup $subject, $result)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  \Magento\Checkout\CustomerData\Cart $checkoutCustomerDataCart */
        $checkoutCustomerDataCart = $objectManager->create('\Magento\Checkout\CustomerData\Cart');
        return parent::afterGetSectionData($checkoutCustomerDataCart, $result);
    }
}

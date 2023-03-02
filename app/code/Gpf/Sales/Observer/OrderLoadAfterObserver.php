<?php

namespace Gpf\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Gpf\Sales\Api\Data\ReceiptInterface;
use Gpf\Sales\Api\ReceiptServiceInterface;

class OrderLoadAfterObserver implements ObserverInterface
{

    /**
     * @var ReceiptServiceInterface
     */
    private $receiptService;

    /**
     * OrderRepository Plugin constructor.
     * @param ReceiptServiceInterface $receiptService
     */
    public function __construct(
        ReceiptServiceInterface $receiptService
    ) {
        $this->receiptService = $receiptService;
    }


    /**
     * @param Observer $observer
     * @return OrderLoadAfterObserver
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var $order OrderInterface */
        $order = $observer->getOrder();
        /** @var $payment OrderPaymentInterface */
        $payment = $order->getPayment();

        if (is_null($payment)) {
            return $this;
        }

        /** @var $extensionAttributes OrderPaymentExtensionInterface * */
        $extensionAttributes = $payment->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }

        $receipt = $this->receiptService->getReceiptForOrder($order);

        $extensionAttributes->setReceiptInfo($receipt);

        $order->getPayment()->setExtensionAttributes($extensionAttributes);

        return $this;
    }

    /**
     * @return OrderPaymentExtensionInterface
     */
    protected function getOrderExtensionDependency()
    {
        $orderPaymentExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderPaymentExtension'
        );

        return $orderPaymentExtension;
    }

}
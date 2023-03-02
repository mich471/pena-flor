<?php

namespace Gpf\Sales\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Gpf\Sales\Api\Data\ReceiptInterface;
use Gpf\Sales\Api\ReceiptServiceInterface;


class OrderRepository
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

    /***
     * @param $subject
     * @param OrderSearchResultInterface $result
     * @return OrderSearchResultInterface
     */
    public function afterGetList($subject, $result)
    {
        foreach ($result->getItems() as $order) {
            $this->addExtensionAttributes($order);
        }
        return $result;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    protected function addExtensionAttributes(OrderInterface $order)
    {
        /** @var $payment OrderPaymentInterface */
        $payment = $order->getPayment();

        /** @var $extensionAttributes OrderPaymentExtensionInterface **/
        $extensionAttributes = $payment->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }

        $receipt = $this->receiptService->getReceiptForOrder($order);

        $extensionAttributes->setReceiptInfo($receipt);

        $order->getPayment()->setExtensionAttributes($extensionAttributes);
        return $order;
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

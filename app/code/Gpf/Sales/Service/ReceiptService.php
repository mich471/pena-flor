<?php

namespace Gpf\Sales\Service;

use Gpf\Sales\Api\Data\ReceiptInterface;
use Gpf\Sales\Api\ReceiptServiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Gpf\Sales\Model\ReceiptFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Gpf\Sales\Helper\Data as DataHelper;

class ReceiptService implements ReceiptServiceInterface
{
    CONST AGGREGATOR_CODE = 'mercadopago_custom_aggregator';

    /**
     * @var ReceiptFactory
     */
    private $receiptFactory;

    /**
     * OrderRepository constructor.
     * @param ReceiptFactory $receiptFactory
     */
    public function __construct(
        ReceiptFactory $receiptFactory
    ) {
        $this->receiptFactory = $receiptFactory;
    }

    /**
     * @param OrderInterface $order
     * @return ReceiptInterface
     */
    public function getReceiptForOrder(OrderInterface $order)
    {
        /** @var $payment OrderPaymentInterface */
        $payment = $order->getPayment();

        /** @var $receipt ReceiptInterface */
        $receipt = $this->receiptFactory->create();
        $receipt->setPaymentMethod($payment->getMethod());

        switch ($payment->getMethod()) {
            case \MercadoPago\Core\Model\Basic\Payment::CODE:
            case \MercadoPago\Core\Model\CustomTicket\Payment::CODE:
            case \MercadoPago\Core\Model\CustomBankTransfer\Payment::CODE:
            case self::AGGREGATOR_CODE:
                $receipt = $this->processInfoForMercadoPago($receipt, $payment);
                break;
            default:
                $receipt->setInstallments('1');
                break;
        }

        return $receipt;
    }

    /**
     * @param ReceiptInterface $receipt
     * @param OrderPaymentInterface $payment
     * @return ReceiptInterface
     */
    protected function processInfoForMercadoPago(ReceiptInterface $receipt, OrderPaymentInterface $payment)
    {
        $additionalInformation = $payment->getAdditionalInformation();

        $installments = $additionalInformation['installments'] ?? 1;
        $receipt->setInstallments(DataHelper::sanitizeInstallments($installments));

        $creditCard = $additionalInformation['payment_method'] ?? '';
        $receipt->setCreditCard(strtoupper($creditCard));

        if (isset($additionalInformation['paymentResponse']) && isset($additionalInformation['paymentResponse']['id'])) {
            $receipt->setMercadoPagoId($additionalInformation['paymentResponse']['id']);
        }
        return $receipt;
    }
}

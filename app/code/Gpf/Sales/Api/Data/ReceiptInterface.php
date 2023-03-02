<?php

namespace Gpf\Sales\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReceiptInterface
 * @api
 */
interface ReceiptInterface extends ExtensibleDataInterface
{

    const INSTALLMENTS_FIELD = 'installments';
    const PAYMENT_METHOD_FIELD = 'payment_method';
    const CREDIT_CARD_FIELD = 'credit_card';
    const MERCADO_PAGO_ID_FIELD = 'mercado_pago_id';

    /**
     * @return string
     */
    public function getInstallments();

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @return string
     */
    public function getCreditCard();

    /**
     * @return mixed|int|string
     */
    public function getMercadoPagoId();

    /**
     * @param mixed|int|string $mercadoPagoId
     * @return ReceiptInterface
     */
    public function setMercadoPagoId($mercadoPagoId);

    /**
     * @param string $installments
     * @return ReceiptInterface
     */
    public function setInstallments($installments);

    /**
     * @param string $method
     * @return ReceiptInterface
     */
    public function setPaymentMethod($method);

    /**
     * @param string $creditCard
     * @return ReceiptInterface
     */
    public function setCreditCard($creditCard);

}

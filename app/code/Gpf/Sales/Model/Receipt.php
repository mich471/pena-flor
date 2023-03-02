<?php

namespace Gpf\Sales\Model;

use Gpf\Sales\Api\Data\ReceiptInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Receipt extends AbstractExtensibleModel implements ReceiptInterface
{

    /**
     * @return string
     */
    public function getInstallments()
    {
        return $this->getData(self::INSTALLMENTS_FIELD);
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->getData(self::PAYMENT_METHOD_FIELD);
    }

    /**
     * @return string
     */
    public function getCreditCard()
    {
        return $this->getData(self::CREDIT_CARD_FIELD);
    }

    /**
     * @return mixed|string|int
     */
    public function getMercadoPagoId()
    {
        return $this->getData(self::MERCADO_PAGO_ID_FIELD);
    }

    /**
     * @param string $installments
     * @return ReceiptInterface
     */
    public function setInstallments($installments)
    {
        $this->setData(self::INSTALLMENTS_FIELD, $installments);

        return $this;
    }

    /**
     * @param string $method
     * @return ReceiptInterface
     */
    public function setPaymentMethod($method)
    {
        $this->setData(self::PAYMENT_METHOD_FIELD, $method);

        return $this;
    }

    /**
     * @param mixed|string|int $mercadoPagoId
     * @return ReceiptInterface
     */
    public function setMercadoPagoId($mercadoPagoId)
    {
        $this->setData(self::MERCADO_PAGO_ID_FIELD, $mercadoPagoId);

        return $this;
    }

    /**
     * @param string $creditCard
     * @return ReceiptInterface
     */
    public function setCreditCard($creditCard)
    {
        $this->setData(self::CREDIT_CARD_FIELD, $creditCard);

        return $this;
    }
}

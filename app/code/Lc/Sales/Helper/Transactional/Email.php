<?php

namespace Lc\Sales\Helper\Transactional;

use Lc\Core\Helper\Transactional\Email as CoreEmail;
use Lc\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Magento\Framework\Event\ManagerInterface;

class Email extends CoreEmail
{

    /**
     * @var PaymentHelper $paymentHelper
     */
    protected $paymentHelper;

    /**
     * @var AddressRenderer $addressRenderer
     */
    protected $addressRenderer;

    /**
     * @var ShipmentIdentity $identityContainer
     */
    protected $identityContainer;

    /**
     * @var ManagerInterface $eventManager
     */
    protected $eventManager;


    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        PaymentHelper $paymentHelper,
        ShipmentIdentity $identityContainer,
        AddressRenderer $addressRenderer
    )
    {
        parent::__construct($context, $storeManager, $inlineTranslation, $transportBuilder);
        $this->paymentHelper = $paymentHelper;
        $this->addressRenderer = $addressRenderer;
        $this->identityContainer = $identityContainer;
        $this->eventManager = $context->getEventManager();
    }

    /**
     * @param Order $order
     * @param string $templatePath
     * @param string $bCc
     * @return $this
     */
    public function sendEmailToCustomer($order, $templatePath, $bCc = null)
    {
        $transport = $this->getEmailDataFromOrder($order);

        $this->eventManager->dispatch(
            'email_shipment_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->setStore($order->getStore())
            ->setPathTemplate($templatePath)
            ->sendEmail(
                $transport,
                $this->getSenderSales(),
                $this->getReceiverInfo($order)
            );

        if ( !is_null($bCc) ) {
            $this->_sendCopyToEmails(explode(",", $bCc), $templatePath, $order);
        }

        return $this;
    }

    /**
     * @param array $emails
     * @param string $templatePath
     * @param Order $order
     * @return $this
     */
    protected function _sendCopyToEmails( $emails, $templatePath, $order)
    {
        foreach ( $emails as $email ) {
            $email = str_replace(" ", "",$email);
            $this->setStore($order->getStore())
                ->setPathTemplate($templatePath)
                ->sendEmail(
                    $this->getEmailDataFromOrder($order),
                    $this->getSenderSales(),
                    [ 'name' => $email, 'email' => $email ]
                );
        }
        return $this;
    }

    /**
     * @param Order $order
     * @return array
     */
    protected function getEmailDataFromOrder(Order $order)
    {
        return [
            'order' => $order,
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order)
        ];
    }

    /**
     * @param Order $order
     * @return array
     */
    protected function getReceiverInfo(Order $order)
    {
        return [
            'name' => $order->getCustomerFirstname()." ".$order->getCustomerLastname(),
            'email' => $order->getCustomerEmail()
        ];
    }

    /**
     * @return array
     */
    protected function getSenderSales()
    {
        return [
            'name' => $this->getConfigValue('trans_email/ident_sales/name', $this->getStore()->getId()),
            'email' => $this->getConfigValue('trans_email/ident_sales/email', $this->getStore()->getId()),
        ];
    }

    /**
     * Returns payment info block as HTML.
     *
     * @param Order $order
     *
     * @return string
     */
    private function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress(Order $order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress(Order $order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
}
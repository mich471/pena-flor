<?php

namespace Gpf\MercadoPago\Model\CustomAggregator;

use Codazon\ThemeOptions\Model\Config\Reader\Store;
use Gpf\MercadoPago\Helper\Data as ConfigHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use MercadoPago\Core\Helper\Data;
use MercadoPago\Core\Model\Api\V1\Exception;
use MercadoPago\Core\Model\Core;

class Payment extends \SummaSolutions\Mercadopago\Model\CustomAggregator\Payment
{
    /**
     * @var ConfigHelper
     */
    protected $configHelper;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Data $helperData
     * @param Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param OrderFactory $orderFactory
     * @param UrlInterface $urlBuilder
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param ModuleListInterface $moduleList
     * @param TimezoneInterface $localeDate
     * @param Core $coreModel
     * @param RequestInterface $request
     * @param ConfigHelper $configHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \MercadoPago\Core\Helper\Data $helperData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \MercadoPago\Core\Model\Core $coreModel,
        \Magento\Framework\App\RequestInterface $request,
        ConfigHelper $configHelper,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct(
            $helperData,
            $checkoutSession,
            $customerSession,
            $orderFactory,
            $urlBuilder,
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $coreModel,
            $request
        );
        $this->configHelper = $configHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $paymentAction
     * @param $stateObject
     * @return true
     * @throws LocalizedException
     * @throws Exception
     */
    public function initialize($paymentAction, $stateObject)
    {
        if ($this->getInfoInstance()->getAdditionalInformation('token') == "") {
            $this->_helperData->log("CustomPaymentAg::initialize - Token for payment creation was not generated, therefore it is not possible to continue the transaction");
            throw new \Magento\Framework\Exception\LocalizedException(__(\MercadoPago\Core\Helper\Response::PAYMENT_CREATION_ERRORS['TOKEN_EMPTY']));
            return $this;
        }

        try {
            $this->_helperData->log("CustomPaymentAg::initialize - Credit Card: init prepare post payment", self::LOG_NAME);

            $infoInstance = $this->getInfoInstance();
            $quote = $this->_getQuote();
            $order = $this->getInfoInstance()->getOrder();
            $payment = $order->getPayment();
            $paymentInfo = $this->getPaymentInfo($payment);

            $preference = $this->_coreModel->makeDefaultPreferencePaymentV1($paymentInfo, $quote, $order);

            //$preference['transaction_amount'] = (float) $quote->getBaseSubtotalWithDiscount() + $quote->getShippingAddress()->getShippingAmount() - $order->getGiftCardsAmount();
            $preference['installments'] = $this->getInstallments($payment);
            $paymentMethod = $payment->getAdditionalInformation("payment_method_id");
            if($paymentMethod == ""){
                // MLM: does not have payment method guessing
                $paymentMethod = $payment->getAdditionalInformation("payment_method_selector");
            }

            $preference['payment_method_id'] = $paymentMethod;
            $preference['token'] = $payment->getAdditionalInformation("token");
            $preference['metadata']['token'] = $payment->getAdditionalInformation("token");

            if($payment->getAdditionalInformation("issuer_id") != "" && $payment->getAdditionalInformation("issuer_id") > -1){
                $preference['issuer_id'] = (int)$payment->getAdditionalInformation("issuer_id");
            }

            if(isset($preference['payer']) && isset($preference['payer']['email'])){

                $this->_accessToken = $this->_helperData->getAccessToken();
                $customer = $this->getOrCreateCustomer($preference['payer']['email']);

                if($payment->getAdditionalInformation("one_click_pay") == 'true' && isset($customer['id'])){
                    $preference['payer']['id'] = $customer['id'];
                }

                //add customer in metadata
                if(isset($customer['id'])){
                    $preference['metadata']['customer_id'] = $customer['id'];
                }
            }

            $preference['binary_mode'] = $this->_scopeConfig->isSetFlag(\SummaSolutions\Mercadopago\Helper\ConfigData::PATH_CUSTOM_AG_BINARY_MODE);
            $preference['statement_descriptor'] = $this->_scopeConfig->getValue(\SummaSolutions\Mercadopago\Helper\ConfigData::PATH_CUSTOM_AG_STATEMENT_DESCRIPTOR);

            $this->_helperData->log("CustomPaymentAg::initialize - Credit Card: Preference to POST /v1/payments", self::LOG_NAME, $preference);
        } catch (\Exception $e) {
            $this->_helperData->log("CustomPaymentAg::initialize - There was an error retrieving the information to create the payment, more details: " . $e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(__(\MercadoPago\Core\Helper\Response::PAYMENT_CREATION_ERRORS['INTERNAL_ERROR_MODULE']));
            return $this;
        }

        // POST /v1/payments
        $response = $this->_coreModel->postPaymentV1($preference);
        $this->_helperData->log("POST /v1/payments RESPONSE", self::LOG_NAME, $response);

        if (isset($response['status']) && ($response['status'] == 200 || $response['status'] == 201)) {

            $payment = $response['response'];
            $infoInstance->setAdditionalInformation("paymentResponse", $payment);

            $fee = $payment['transaction_details']['total_paid_amount'] - $order->getGrandTotal();
            if ($fee){
                $this->_registry->unregister('mercadopago_clear_totals');
                $this->_checkoutSession->setData('mercadopago_total_amount', $fee);
                $quote->setTotalsCollectedFlag(false);
                $quote->collectTotals();
                $order->setFinanceCostAmount($quote->getShippingAddress()->getFinanceCostAmount());
                $order->setBaseFinanceCostAmount($quote->getShippingAddress()->getBaseFinanceCostAmount());
                $order->setGrandTotal($quote->getGrandTotal())
                    ->setBaseGrandTotal($quote->getBaseGrandTotal())
                    ->setTotalDue($quote->getBaseGrandTotal())
                    ->setBaseTotalDue($quote->getBaseGrandTotal());
            }

            return true;

        }else{
            $messageErrorToClient = $this->_coreModel->getMessageError($response);

            $arrayLog = array(
                "response" => $response,
                "message" => $messageErrorToClient
            );

            $this->_helperData->log("CustomPaymentAg::initialize - The API returned an error while creating the payment, more details: " . json_encode($arrayLog));

            throw new \Magento\Framework\Exception\LocalizedException(__($messageErrorToClient));

            return $this;
        }
    }

    /**
     * No production environment purpose, it was made as a workaround for cases where
     * installments could not be retrieved from MercadoPago api
     *
     * @param $payment
     * @return int
     * @throws NoSuchEntityException
     */
    public function getInstallments($payment): int
    {
        if ($this->configHelper->getDeactivateInstallments($this->storeManager->getStore()->getId())) {
            return 1;
        }

        return (int)$payment->getAdditionalInformation("installments");
    }
}

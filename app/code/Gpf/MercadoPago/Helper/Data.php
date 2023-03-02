<?php

namespace Gpf\MercadoPago\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const DEACTIVATE_INSTALLMENTS = "payment/mercadopago_configurations/custom_checkout_ag/deactivate_installments";

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;


    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getStatusApproved($storeId = null)
    {
        return $this->getConfigValue('payment/mercadopago/order_status_approved', $storeId);
    }

    public function getDeactivateInstallments($storeId = null)
    {
        return $this->getConfigValue(self::DEACTIVATE_INSTALLMENTS, $storeId);
    }

    /**
     * @param string $path
     * @param int $storeId
     * @return mixed
     **/
    public function getConfigValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

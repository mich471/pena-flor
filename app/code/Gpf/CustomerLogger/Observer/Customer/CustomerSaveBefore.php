<?php

namespace Gpf\CustomerLogger\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Gpf\CustomerLogger\Helper\Data as HelperData;

class CustomerSaveBefore implements ObserverInterface
{
    const CUSTOMER_COMPANY_NAME_ATTRIBUTE = 'nombre_empresa';

    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Http
     */
    protected $request;


    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * RegisterSuccessLog constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Http $request,
        StoreManagerInterface $storeManager,
        HelperData $helperData
    )
    {
        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
    }

    /**
     * @return \Zend\Log\Logger
     */
    protected function logger()
    {
        if (is_null($this->logger)) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer_register_cname.log');
            $this->logger = new \Zend\Log\Logger();
            $this->logger->addWriter($writer);
        }
        return $this->logger;
    }

    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $customer = $observer->getEvent()->getData('customer');

        if (is_null($customer)) {
            return $this;
        }

        try {
            $attribute = $this->customerRepository->getById($customer->getId())->getCustomAttribute(self::CUSTOMER_COMPANY_NAME_ATTRIBUTE);
            $this->logger()->debug(
                json_encode([
                    'Event' => 'customer_save_before',
                    'CustomerEMAIL' => $customer->getEmail(),
                    'Customer_action_url' => $this->request->getFullActionName(),
                    'CustomerID' => $customer->getId(),
                    'Params' => $this->request->getParam(self::CUSTOMER_COMPANY_NAME_ATTRIBUTE),
                    'Repository' => (!is_null($attribute) ? $attribute->getValue() : ''),
                    'StoreCode' => $this->storeManager->getStore()->getCode()
                ]));
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }
}
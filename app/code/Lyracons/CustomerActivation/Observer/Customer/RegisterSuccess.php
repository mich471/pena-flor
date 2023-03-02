<?php

namespace Lyracons\CustomerActivation\Observer\Customer;

use Lyracons\CustomerActivation\Helper\Data as HelperData;
use Lyracons\CustomerActivation\Model\Email;
use Lyracons\CustomerActivation\Observer\AbstractObserver;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Psr\Log\LoggerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

class RegisterSuccess extends AbstractObserver implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface $customerRepository
     */
    protected $customerRepository;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * RegisterSuccess constructor.
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param Email $emailModel
     * @param Registry $registry
     * @param MessageManagerInterface $messageManager
     * @param CustomerSession $customerSession
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        Email $emailModel,
        Registry $registry,
        MessageManagerInterface $messageManager,
        CustomerSession $customerSession
    )
    {
        parent::__construct($helperData, $storeManager, $logger, $customerRepository, $emailModel,$registry);
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var $customer CustomerInterface|Customer */
        $customer = $observer->getEvent()->getData('customer');
        if (!$this->helperData->isModuleActive()) {
            return $this;
        }
        try {
            $newCustomer = $this->customerRepository->get($customer->getEmail());
            $newCustomer->setCustomAttribute(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE, 0);
            $newCustomer->setCustomAttribute(AbstractObserver::ADMIN_NOTIFY_PENDING_ATTRIBUTE, 1);

            $this->customerRepository->save($newCustomer);
            $this->customerSession->setRegisterSuccess(true);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return $this;
    }
}

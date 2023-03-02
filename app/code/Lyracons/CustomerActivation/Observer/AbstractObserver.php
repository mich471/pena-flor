<?php

namespace Lyracons\CustomerActivation\Observer;

use Lyracons\CustomerActivation\Helper\Data as HelperData;
use Lyracons\CustomerActivation\Model\Email;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

abstract class AbstractObserver
{
    const CUSTOMER_ACTIVATION_ATTRIBUTE = 'customeractivation_is_active';
    const ADMIN_NOTIFY_PENDING_ATTRIBUTE = 'admin_notify_pending';

    /**
     * @var HelperData $helperData
     */
    protected $helperData;

    /**
     * @var BackendSession $backendSession
     */
    protected $backendSession;

    /**
     * @var StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Email
     */
    protected $emailModel;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * SaveBefore constructor.
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param Email $emailModel
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        Email $emailModel,
        \Magento\Framework\Registry $registry
    )
    {
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->emailModel = $emailModel;
        $this->registry = $registry;
    }
}

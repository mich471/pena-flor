<?php

namespace Lyracons\CustomerActivation\Cron;

use Lyracons\CustomerActivation\Model\Email;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoresConfig;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Lyracons\CustomerActivation\Helper\Data as HelperData;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

/**
 * Class NotifyToAdministrator
 */
class NotifyToAdministrator
{

    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Email
     */
    protected $emailModel;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Customer|CustomerInterface
     */
    protected $currentCustomer;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param State $state
     * @param StoresConfig $storesConfig
     * @param CollectionFactory $collectionFactory
     * @param HelperData $helperData
     * @param Email $emailModel
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        State $state,
        StoresConfig $storesConfig,
        CollectionFactory $collectionFactory,
        HelperData $helperData,
        Email $emailModel,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->state = $state;
        $this->storesConfig = $storesConfig;
        $this->collectionFactory = $collectionFactory;
        $this->emailModel = $emailModel;
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Notify to administrator
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute()
    {
        /** @var $collection CustomerCollection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('admin_notify_pending', ['eq' => 1]);
        /** @var $item Customer|CustomerInterface * */
        foreach ($collection->getItems() as $item) {
            $this->state->emulateAreaCode(Area::AREA_FRONTEND, function ($item) {
                if(
                    $this->helperData->isModuleActive($item->getStoreId()) 
                    && $this->helperData->getAdminNotifyOnRegister($item->getStoreId()) 
                ){
                    $emails = $this->helperData->getAdminNotifyTo($item->getStoreId());
                    $emails = explode(",", $emails);
                    foreach ($emails as $email) {
                        $this->emailModel->sendEmailToAdministrator($item,preg_replace('/\s+/', '', $email));
                    }    
                }
            }, ['item' => $item]);

            $customer = $this->customerRepository->getById($item->getId());
            $customer->setCustomAttribute('admin_notify_pending', 0);
            $this->customerRepository->save($customer);
        }
    }

}
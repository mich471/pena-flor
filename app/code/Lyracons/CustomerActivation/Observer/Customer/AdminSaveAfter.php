<?php

namespace Lyracons\CustomerActivation\Observer\Customer;

use Lyracons\CustomerActivation\Observer\AbstractObserver;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AdminSaveAfter extends AbstractObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var $customer CustomerInterface|Customer */
        $customer = $observer->getEvent()->getData('customer');

        if (!$this->helperData->isModuleActive($customer->getStoreId())
            || $customer->isObjectNew()
        ) {
            return $this;
        }

        $customerOrigData = $this->registry->registry(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE);
        $this->registry->unregister(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE);
        $customerNewData = $customer->getData(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE);

        if (!$customerOrigData && $customerNewData) {
            try {
                $this->emailModel->sendEmailToCustomer($customer);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $this;
    }

}

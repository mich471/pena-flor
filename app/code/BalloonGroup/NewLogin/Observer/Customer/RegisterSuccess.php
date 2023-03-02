<?php

namespace BalloonGroup\NewLogin\Observer\Customer;

use Lyracons\CustomerActivation\Observer\AbstractObserver;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;

class RegisterSuccess extends \Lyracons\CustomerActivation\Observer\Customer\RegisterSuccess
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer): RegisterSuccess
    {
        /** @var $customer CustomerInterface|Customer */
        $customer = $observer->getEvent()->getData('customer');
        if (!$this->helperData->isModuleActive()) {
            return $this;
        }
        try {
            $newCustomer = $this->customerRepository->get($customer->getEmail());
            $newCustomer->setCustomAttribute(
                AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE,
                (bool)$this->customerSession->getIsPreApproved()
            );
            $newCustomer->setCustomAttribute(
                AbstractObserver::ADMIN_NOTIFY_PENDING_ATTRIBUTE,
                (bool)!$this->customerSession->getIsPreApproved()
            );

            $this->customerRepository->save($newCustomer);
            $this->customerSession->setRegisterSuccess(true);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $this;
    }
}

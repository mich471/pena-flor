<?php

namespace Lyracons\CustomerActivation\Observer\Customer;

use Lyracons\CustomerActivation\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AdminSaveBefore extends AbstractObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getData('customer');
        if (!$customer->getEntityId()) {
            $this->registry->register(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE, 0);
            return $this;
        }

        $dataOrigValue = 0;
        $customer = $this->customerRepository->getById($customer->getId());

        if ($attribute = $customer->getCustomAttribute(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE)) {
            $dataOrigValue = $attribute->getValue();
        }

        if (!is_null($this->registry->registry(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE))) {
            $this->registry->unregister(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE);
        }

        $this->registry->register(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE, $dataOrigValue);

        return $this;
    }
}

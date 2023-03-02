<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use SummaSolutions\WebShopAppsMatrixRate\Api\RateRepositoryInterface;

class SalesOrderPlaceBefore implements ObserverInterface
{
    const MATRIX_RATE_METHOD_PREFIX = 'matrixrate_matrixrate_';
    const NO_EXTERNAL_ID = 'N/A';

    /**
     * @var RateRepositoryInterface
     */
    protected RateRepositoryInterface $rateRepository;

    /**
     * @param RateRepositoryInterface $rateRepository
     */
    public function __construct(
        RateRepositoryInterface $rateRepository
    )
    {
        $this->rateRepository = $rateRepository;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getOrder();
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod && str_contains($shippingMethod, self::MATRIX_RATE_METHOD_PREFIX)) {
            $order->setExternalShippingMethodId($this->getExternalMethodId($shippingMethod));
        }

        return $this;
    }

    /**
     * @param string $shippingMethod
     * @return string
     */
    protected function getExternalMethodId(string $shippingMethod) : string
    {
        $rateId = str_replace(self::MATRIX_RATE_METHOD_PREFIX,"", $shippingMethod);

        try {
            $rate = $this->rateRepository->get($rateId);
        } catch (\Exception $e) {
            return self::NO_EXTERNAL_ID;
        }

        return $rate->getShippingMethod();
    }

}

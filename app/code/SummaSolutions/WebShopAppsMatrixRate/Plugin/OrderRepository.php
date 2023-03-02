<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use SummaSolutions\WebShopAppsMatrixRate\Observer\SalesOrderPlaceBefore;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepository
{

    /**
     * @var OrderExtensionFactory
     */
    protected OrderExtensionFactory $orderExtensionFactory;


    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $result
    ) {
        return $this->getExternalShipping($result);
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $result
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $result
    )
    {
        $orders = $result->getItems();

        foreach ($orders as $order) {
            $this->getExternalShipping($order);
        }

        return $result;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    private function getExternalShipping(OrderInterface $order)
    {
        try {
            $externalShippingMethod = $order->getData('external_shipping_method_id');
        } catch (\Exception $e) {
            $externalShippingMethod = null;
        }

        if (!$externalShippingMethod) {
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();

        if (!$extensionAttributes) {
            return $order;
        }

        /// currently there is only one shipping assignment per order so there's no need to iterate
        /// see vendor/magento/module-sales/Model/Order/ShippingAssignmentBuilder.php:88
        $shippingAssignment = $extensionAttributes->getShippingAssignments()[0];

        if (!$shippingAssignment)
        {
            return $order;
        }

        $shippingAssignment->getShipping()->setMethod($externalShippingMethod);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}

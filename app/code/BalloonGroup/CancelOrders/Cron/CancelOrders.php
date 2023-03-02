<?php

namespace BalloonGroup\CancelOrders\Cron;

use BalloonGroup\General\Logger\Logger;
use Lyracons\CancelOrders\Block\Adminhtml\Form\Field\TimeTypes;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoresConfig;
use Magento\Sales\Api\OrderRepositoryInterface;

class CancelOrders extends \Lyracons\CancelOrders\Cron\CancelOrders
{

    protected $storesConfig;
    protected $orderCollectionFactory;
    protected $orderManagement;
    protected $serializer;
    protected $orderRepository;

    /**
     * CancelOrders constructor.
     * @param StoresConfig $storesConfig
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderManagementInterface|null $orderManagement
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        StoresConfig $storesConfig,
        CollectionFactory $orderCollectionFactory,
        OrderManagementInterface $orderManagement,
        SerializerInterface $serializer,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->storesConfig = $storesConfig;
        $this->orderManagement = $orderManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
        parent::__construct($storesConfig, $orderCollectionFactory, $orderManagement, $serializer);
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $orders = $this->orderCollectionFactory->create();
        $enableds = $this->storesConfig->getStoresConfigByPath('sales/orders/cancelorders_enabled');
        $lifetimes = $this->storesConfig->getStoresConfigByPath('sales/orders/cancelorders_lifetimes');
        $idx = 1;
        foreach ($enableds as $storeId => $enabled) {
            if (!$enabled) {
                continue;
            }

            $arrayConfig = empty($lifetimes[$storeId]) ? null : $this->serializer->unserialize($lifetimes[$storeId]);
            if ($arrayConfig == null) {
                continue;
            }
            $arrayConfig = array_values($arrayConfig);
            foreach ($arrayConfig as $row) {
                $lifetime = $row['lifetime'];
                if ($row['time_type'] == TimeTypes::TYPE_MINUTES)
                    $lifetime = $lifetime * 60;
                if ($row['time_type'] == TimeTypes::TYPE_HOURS)
                    $lifetime = $lifetime * 60 * 60;
                if ($row['time_type'] == TimeTypes::TYPE_DAYS)
                    $lifetime = $lifetime * 24 * 60 * 60;

                $where = implode(' AND ', [
                    'TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) >= :lifetime' . $idx,
                    'status = :status' . $idx,
                    'store_id = :storeId' . $storeId
                ]);
                $orders->getSelect()->orWhere($where);
                $orders->addBindParam('lifetime' . $idx, $lifetime);
                $orders->addBindParam('status' . $idx, $row['status']);
                $idx++;
            }
            $orders->addBindParam('storeId' . $storeId, $storeId);
        }

        foreach ($orders->getAllIds() as $entityId) {
            $objectManager = ObjectManager::getInstance();
            $logger = $objectManager->get(Logger::class);
            $logger->info('GDEBUG ' . __FILE__ . ':' . __LINE__ . '/' . __METHOD__);

            try {
                $this->orderManagement->cancel((int)$entityId);
                $order = $this->orderRepository->get($entityId);
                $order->addStatusHistoryComment('Order canceled by cron from module-cancel-orders');
                $order->save();

            } catch (\Exception $e) {
                $logger->print('Error en cron de cancelación: ' . $e->getMessage());
                $logger->print('Order id: ' . $entityId);
            }
        }
    }
}

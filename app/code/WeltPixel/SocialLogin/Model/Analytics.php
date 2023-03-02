<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Analytics
 * @package WeltPixel\SocialLogin\Model
 */
class Analytics
{
    /**
     * @var array
     */
    protected $_socialMedia = [
        'fb' => 'Facebook',
        'amazon' => 'Amazon',
        'google' => 'Google',
        'instagram' => 'Instagram',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'paypal' => 'PayPal',
        'default' => 'Email & Password',
        'guest' => 'Guest'
    ];
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var OrderUserFactory
     */
    protected $orderUserFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var SocialloginFactory
     */
    protected $socialLoginModel;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerFactory;

    /**
     * @var
     */
    protected $_dataObjectArr = [];

    /**
     * @var
     */
    protected $_model;

    /**
     * @var
     */
    protected $_type;

    /**
     * @var
     */
    protected $_excludedSocialCustomerIds;

    /**
     * @var
     */
    protected $_excludedOrderIds;

    /**
     * @var
     */
    protected $_orderIdsByType;

    /**
     * @var
     */
    protected $_orders;

    /**
     * @var
     */
    protected $_ordersFiltered;

    /**
     * @var
     */
    protected $_orderUserModel;

    /**
     * @var Report
     */
    protected $_reportModel;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var
     */
    protected $condition;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serializer;

    /**
     * Analytics constructor.
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param SocialloginFactory $socialLoginModel
     */
    public function __construct(
        \Magento\Framework\DataObjectFactory $objectFactory,
        \WeltPixel\SocialLogin\Model\SocialloginFactory $socialLoginModel,
        \WeltPixel\SocialLogin\Model\OrderUserFactory $orderUserFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \WeltPixel\SocialLogin\Model\Report $reportModel,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Serialize\Serializer\Serialize $serializer
    )
    {
        $this->objectFactory = $objectFactory;
        $this->socialLoginModel = $socialLoginModel;
        $this->_customerFactory = $customerFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->orderUserFactory = $orderUserFactory;
        $this->_reportModel = $reportModel;
        $this->_resourceConnection = $resourceConnection;
        $this->_priceHelper = $priceHelper;
        $this->_serializer = $serializer;
    }

    /**
     * @return array
     */
    public function getAnalyticsData() {
        return $this->_setDataOject();
    }

    /**
     * @return mixed
     */
    public function getAnalyticsTotals() {
        return $this->_setTotalDataObject();
    }

    /**
     * set analytics data
     */
    public function setAnalyticsData() {
        $this->_setDataOject();
        $this->_setTotalDataObject();
    }

    /**
     * @return array
     */
    protected function _setDataOject() {

        $this->_model = $this->socialLoginModel->create();
        $this->_orderUserModel = $this->orderUserFactory->create();
        $this->_excludedSocialCustomerIds = $this->_model->getCustomerIdArr();
        $this->_excludedOrderIds = $this->_orderUserModel->getOrderIdsArr();

        $this->_reportModel->truncateWpSlAnalytics();

        foreach ($this->_socialMedia as $type => $label) {
            $this->_type = $type;
            $this->_orderIdsByType = $this->_orderUserModel->getOrderIdsByType($this->_type);
            $this->_setAllOrdersCollection();
            $this->_setFilteredOrdersCollection();
            $this->_setQueryConditions();
            $dataObject = $this->objectFactory->create();

            $userCount = ($this->_type == 'guest') ? '-' : $this->_countUsers();
            $userPercent = ($this->_type == 'guest') ? '-' : $this->_calculatePercent($this->_countAllCustomers(), $userCount);
            $userOrders = $this->_countOrders();
            $userOrdersPercent = $this->_calculatePercent($this->_countAllOrders(), $userOrders);
            $orderItems = $this->_countItems();
            $orderItemsPercent = $this->_calculatePercent($this->_countAllOrdersItems(), $orderItems);
            $revenue = $this->_getRevenue();
            $formatedRevenue = $this->customPriceFormat($revenue);
            $revenuePercent = $this->_calculatePercent($this->_getTotalRevenues(), $revenue);


            $dataObject->setUsersNo($userCount);
            $dataObject->setUsersPrecent($userPercent);
            $dataObject->setOrdersNo($userOrders);
            $dataObject->setOrdersPercent($userOrdersPercent);
            $dataObject->setItemsNo($orderItems);
            $dataObject->setItemsPercent($orderItemsPercent);
            $dataObject->setRevenue($formatedRevenue);
            $dataObject->setRevenuePercent($revenuePercent);


            $this->_dataObjectArr[$label] = $dataObject;

            $this->_reportModel->setReportData($this->_type, $this->_serializer->serialize($dataObject->getData()));
        }

        return $this->_dataObjectArr;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function _setTotalDataObject() {
        $totalDataObject = $this->objectFactory->create();
        $totalDataObject->setTotalCustomers($this->_countAllCustomers());
        $totalDataObject->setTotalOrders($this->_countAllOrders());
        $totalDataObject->setTotalOrdersItems($this->_countAllOrdersItems());
        $totalDataObject->setTotalRevenue($this->customPriceFormat($this->_getTotalRevenues()));

        $this->_reportModel->setReportData('total',  $this->_serializer->serialize($totalDataObject->getData()));

        return $totalDataObject;
    }

    /**
     * @return mixed
     */
    protected function _countUsers() {
        if($this->_type == 'default') {
            return $this->_countDefaultCustomers();
        }

        return $this->_model->countUsersByType($this->_type);
    }

    protected function _countDefaultCustomers() {

        $collection = $this->_customerFactory->create()
            ->addFieldToFilter(
                'entity_id', ['nin' => $this->_excludedSocialCustomerIds]
            );

        return $collection->getSize();
    }

    /**
     * @return mixed
     */
    protected function _countAllCustomers() {
        $collection = $this->_customerFactory->create();

        return $collection->getSize();
    }

    /**
     * @param $customerId
     * @return mixed
     */
    protected function _countOrders() {
        if($this->_type == 'default') {
            $orderIds = $this->_excludedOrderIds;
            $orders = $this->_orders;
            if(!empty($orderIds)) {
                $orders->addFieldToFilter('entity_id',['nin' => $orderIds]);
            }
            $orders->addFieldToFilter('customer_id',['neq' => 'NULL']);
        } elseif($this->_type == 'guest') {
            $orders = $this->_orders->addFieldToFilter('customer_id',['null' => true]);
        } else {
            $orders = $this->_orderUserModel->getCollection()->addFieldToFilter('type', $this->_type);
        }

        return $orders->getSize();
    }


    /**
     * @return mixed
     */
    protected function _countAllOrders() {
        $collection = $this->_orderCollectionFactory->create();

        return $collection->getSize();
    }

    /**
     * @return mixed
     */
    protected function _setFilteredOrdersCollection() {

        if($this->_type == 'default') {
            $orderIds = $this->_excludedOrderIds;
            $orders = $this->_orders;
            if(!empty($orderIds)) {
                $orders->addFieldToFilter('entity_id',['nin' => $orderIds]);
            }
            $orders->addFieldToFilter('customer_id',['neq' => 'NULL']);
            $this->_ordersFiltered = $orders;
        } elseif($this->_type == 'guest') {
            $this->_ordersFiltered  = $this->_orders
                ->addFieldToFilter('customer_id',['null' => true]);
        } else {
            $orderIdsByType = $this->_orderIdsByType;
            $this->_ordersFiltered = $this->_orders
                ->addFieldToFilter('entity_id',['in' => $orderIdsByType]);
        }
    }

    protected function _setQueryConditions() {

        if($this->_type == 'default') {
            $orderIds = $this->_excludedOrderIds;
            if(!empty($orderIds)) {
                $orderIdsStr = implode(', ', $orderIds);
                $this->condition = ' AND entity_id NOT IN('.$orderIdsStr.')';
            }
            $this->condition .= ' AND customer_id IS NOT NULL';
        } elseif($this->_type == 'guest') {
            $this->condition = ' AND customer_id IS NULL';
        } else {
            $orderIdsByType = (!empty($this->_orderIdsByType)) ? implode(', ', $this->_orderIdsByType) : '';
            $this->condition = ($orderIdsByType) ? ' AND entity_id IN('.$orderIdsByType.')' : '';
        }

    }



    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function _setAllOrdersCollection() {
        $this->_orders = $this->_orderCollectionFactory->create();
        return $this->_orders;
    }

    /**
     * @return int|string
     */
    protected function _countItems() {
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order');

        if(!$this->condition) {
            return 0;
        }

        $sql = "SELECT SUM(total_item_count) as items FROM " . $tableName ." WHERE state NOT IN('pending_payment', 'canceled') " . $this->condition;
        $result = $connection->fetchOne($sql);

        $result = ($result) ?? 0;

        return $result;
    }

    /**
     * @return string
     */
    protected function _countAllOrdersItems() {
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order');

        $sql = "SELECT SUM(total_item_count) as items FROM " . $tableName ." WHERE state NOT IN('pending_payment', 'canceled')";
        $result = $connection->fetchOne($sql);

        return $result;
    }

    /**
     * Calculation based on 'based_grand_total'
     * @return int
     */
    protected function _getRevenue()
    {
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order');

        if(!$this->condition) {
            return 0;
        }

        $sql = "SELECT SUM(base_grand_total) as revenue FROM " . $tableName ." WHERE state NOT IN('pending_payment', 'canceled') " . $this->condition;
        $result = $connection->fetchOne($sql);

       return $result;
    }


    /**
     * @return string
     */
    protected function _getTotalRevenues() {
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('sales_order');

        $sql = "SELECT SUM(base_grand_total) as revenue FROM " . $tableName ." WHERE state NOT IN('pending_payment', 'canceled')";
        $result = $connection->fetchOne($sql);

        return $result;
    }

    /**
     * @param $total
     * @param $number
     * @return float
     */
    private function _calculatePercent($total, $number) {
        if((int)$number > 0) {
            $prec = ($number * 100) / $total;

            return round($prec, 3);
        } else {
            return 0;
        }

    }

    /**
     * @param $number
     * @return string
     */
    private function customPriceFormat($number) {
        return $this->_priceHelper->currency((int)$number, true, false);
    }


}

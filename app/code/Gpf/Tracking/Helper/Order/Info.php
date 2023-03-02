<?php

namespace Gpf\Tracking\Helper\Order;

use Gpf\Tracking\Helper\Data as DataHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class Info extends DataHelper
{

    /**
     * @var CheckoutSession\ $_checkoutSession
     */
    protected $_checkoutSession;

    /**
     * @var Quote $_quote
     */
    protected $_quote;

    /**
     * @var OrderInterface $_order
     */
    protected $_order;

    /**
     * @var OrderRepository $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var SearchCriteriaBuilder $_searchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * Info constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CheckoutSession $checkoutSession
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context, $storeManager);
        $this->_checkoutSession = $checkoutSession;
        $this->_quote = $this->_checkoutSession->getQuote();
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_order = null;
    }


    /**
     * @param string $type
     * @return array
     */
    public function getOrderInfo($type = 'Lead')
    {
        $items = $this->_order->getItems();
        $products = '';
        $qty = 0;
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $products .= $item->getName()."|";
            $qty += $item->getQtyOrdered();
        }

        return [
            '_setType' => $type,
            '_setDomain' => 'www.news.mascotavineyards.com.ar',
            '_itemsQuantity' => $qty,
            '_amount' => $this->_order->getGrandTotal(),
            '_extraInfo' => 'trx: '.$this->_order->getIncrementId(),
        ];
    }

    /**
     * @param string $type
     * @return array
     */
    public function getOrderInfoForSerialize($type = 'Lead')
    {
        $items = $this->_order->getItems();
        $products = '';
        $qty = 0;
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $products .= $item->getName()."|";
            $qty += $item->getQtyOrdered();
        }

        return [
            'opt' => $type,
            'domain' => 'www.news.mascotavineyards.com.ar',
            'itemsQuantity' => $qty,
            'amount' => $this->_order->getGrandTotal(),
            'extraInfo' => 'trx: '.$this->_order->getIncrementId(),
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    public function getJsonOrderInfo($type = 'Lead')
    {
        return json_encode($this->getOrderInfo($type));
    }

    /**
     * @param string $type
     * @return string
     */
    public function getSerializedOrderInfo($type = 'Lead')
    {
        $serialized = '';
        foreach ($this->getOrderInfo($type) as $key => $value) {
            $serialized .= $key.'='.$value.'&';
        }

        return rtrim($serialized, '&');
    }

    public function setOrderIncrementId($incrementId)
    {
        $searchCriteria = $this->_searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId, 'eq')->create();

        /** @var $orderList OrderCollection */
        $orderList = $this->_orderRepository->getList($searchCriteria);

        if ($orderList->count()) {
            $this->_order = $orderList->getFirstItem();
        }

        return $this->_order;
    }

}
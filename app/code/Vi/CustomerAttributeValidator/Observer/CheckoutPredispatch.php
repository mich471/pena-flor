<?php 

namespace Vi\CustomerAttributeValidator\Observer;

use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Message\ManagerInterface as MessageInterface;

// @Flag: Esto se depreca en la version de magento > 2.2.2

class CheckoutPredispatch implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RedirectInterface
     */
    protected $redirectInterface;
    
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetaInterface;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var MessageInterface
     */
    protected $messageInterface;

    /**
     * @var LoggerInterface $logger
     * @var ProductMetadataInterface $productMetaInterface
     * @var StoreManagerInterface $storeManagerInterface
     * @var CustomerSession $customerSession
     * @var CustomerRepository $customerRepository
     * @var MessageInterface $messageInterface
     */
    public function __construct(
        LoggerInterface $logger,
        ProductMetadataInterface $productMetaInterface,
        StoreManagerInterface $storeManagerInterface,
        CustomerSession $customerSession,
        CustomerRepository $customerRepository,
        MessageInterface $messageInterface,
        RedirectInterface $redirectInterface
    ) {
        $this->logger = $logger;
        $this->productMetaInterface = $productMetaInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->messageInterface = $messageInterface;
        $this->redirectInterface = $redirectInterface;
    }

    /**
     * @var Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (
            version_compare($this->productMetaInterface->getVersion(), '2.1.4') === 1 ||
            $this->storeManagerInterface->getStore()->getCode() !== 'ventainstitucional'
        ) {
            return;
        }
        $controller = $observer->getControllerAction();
        $customer = $this->customerRepository->getById(
            $this->customerSession->getCustomerId()
        );
        $attributeValue = $customer->getCustomAttribute('nombre_empresa');
        if (!$attributeValue || $attributeValue->getValue() === '') {
            $this->messageInterface->addError(__("bussiness_name is required"));
            $this->redirectInterface->redirect($controller->getResponse(), 'customer/account/edit');
        }
    }
}
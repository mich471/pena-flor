<?php
namespace WeltPixel\AdvancedWishlist\Controller\View;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Collections
 * @package WeltPixel\AdvancedWishlisy\Controller\View
 */
class Collections extends Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Editable constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface
     */
    public function execute()
    {
        $customer = $this->initCustomer();
        if (!$customer) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $customerWishlistsBlock = $resultLayout->getLayout()->getBlock('advancedwishlist.customer.collections');
        if ($customerWishlistsBlock) {
            $userProfileId = (int)$this->getRequest()->getParam('profileid');
            $customerWishlistsBlock->setCustomer($customer);
            $customerWishlistsBlock->setProfileId($userProfileId);
            if ($this->customerSession->isLoggedIn()) {
                $loggedInCustomerId = $this->customerSession->getCustomer()->getId();
                $canEditWishlistFlag = $this->getRequest()->getParam('canEditWishlist', false);
                $customerWishlistsBlock->setLoggedInCustomerId($loggedInCustomerId);
                $customerWishlistsBlock->setCanEditWishlistFlag($canEditWishlistFlag);
            }
        }

        return $resultLayout;
    }

    /**
     * Initialize and check customer
     *
     * @return Customer|bool
     */
    protected function initCustomer()
    {
        $customerId = (int)$this->getRequest()->getParam('id');

        $customer = $this->loadCustomer($customerId);
        if (!$customerId) {
            return false;
        }

        return $customer;
    }

    /**
     * @param int $customerId
     * @return bool|Customer
     */
    protected function loadCustomer($customerId)
    {
        if (!$customerId) {
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $noEntityException) {
            return false;
        }
        return $customer;
    }
}

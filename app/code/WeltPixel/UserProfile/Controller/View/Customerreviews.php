<?php
namespace WeltPixel\UserProfile\Controller\View;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use WeltPixel\UserProfile\Model\UserProfileFactory;


class Customerreviews extends Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * Editable constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserProfileFactory $userProfileFactory
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        UserProfileFactory $userProfileFactory
    )
    {
        parent::__construct($context);
        $this->customerRepository = $customerRepository;
        $this->userProfileFactory = $userProfileFactory;
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
        $customerReviewsBlock = $resultLayout->getLayout()->getBlock('userprofile.customer.reviews');
        if ($customerReviewsBlock) {
            $userProfile = $this->userProfileFactory->create()->loadByCustomerId($customer->getId());
            $customerReviewsBlock->setCustomer($customer);
            $customerReviewsBlock->setUserProfile($userProfile);
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

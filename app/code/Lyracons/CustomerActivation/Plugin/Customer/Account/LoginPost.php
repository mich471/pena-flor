<?php

namespace Lyracons\CustomerActivation\Plugin\Customer\Account;


use Lyracons\CustomerActivation\Observer\AbstractObserver;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Lyracons\CustomerActivation\Helper\Data as HelperData;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;


class LoginPost
{
    /**
     * @var RedirectFactory $resultRedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RedirectInterface $redirect
     */
    protected $redirect;

    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var CustomerSession $customerSession
     */
    protected $customerSession;

    /**
     * @var HelperData $helperData
     */
    private $helperData;
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;


    /**
     * CreatePost constructor.
     * @param HelperData $helperData
     * @param RedirectFactory $redirectFactory
     * @param RedirectInterface $redirectInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSession $customerSession
     * @param MessageManagerInterface $messageManager
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        HelperData $helperData,
        RedirectFactory $redirectFactory,
        RedirectInterface $redirectInterface,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        MessageManagerInterface $messageManager,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->resultRedirectFactory = $redirectFactory;
        $this->redirect = $redirectInterface;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
    }


    /**
     * @param $subject
     * @param $result
     * @return ResultRedirect
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute($subject, $result)
    {
        if (!$this->helperData->isModuleActive()) {
            return $result;
        }
        $lastCustomerId = $this->customerSession->getCustomerId();

        if ($lastCustomerId) {
            /** @var $customer Magento\Customer\Model\Customer **/
            $customerSession = $this->customerSession->getCustomer();
            $customer = $this->customerRepository->getById($customerSession->getId());

            if ($attribute = $customer->getCustomAttribute(AbstractObserver::CUSTOMER_ACTIVATION_ATTRIBUTE)) {
                if((int)$attribute->getValue() == 1){
                    return $result;
                }
            }
            $this->customerSession->logout()->setBeforeAuthUrl($this->redirect->getRefererUrl())
                ->setLastCustomerId($lastCustomerId);

            if ($subject->getRequest()->getUri()->getPath() == "/customer/account/confirm/") {
                $this->messageManager->addNoticeMessage(__('You have validated your email, but account is still pending validation from admin'));
            } else {
                $this->messageManager->addNoticeMessage(__('Your account has not been enabled yet'));
            }

            /** @var ResultRedirect $resultRedirect */
            $result = $this->resultRedirectFactory->create();
            $result->setPath('*/*/logoutSuccess');
        }
        return $result;
    }

}

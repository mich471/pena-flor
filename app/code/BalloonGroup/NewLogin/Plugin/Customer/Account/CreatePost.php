<?php

namespace BalloonGroup\NewLogin\Plugin\Customer\Account;

use BalloonGroup\NewLogin\Model\EmailDomain;
use Closure;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use BalloonGroup\NewLogin\Model\ResourceModel\Dni\CollectionFactory as DniCollectionFactory;
use BalloonGroup\NewLogin\Model\ResourceModel\Dni\Collection as DniCollection;
use BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain\CollectionFactory as DomainCollectionFactory;
use BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain\Collection as DomainCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreatePost
{

    const EXISTING_CUSTOMER_ERROR = "customerAlreadyExistsErrorMessage";

    /**
     * @var RedirectFactory $resultRedirectFactory
     */
    protected RedirectFactory $resultRedirectFactory;
    /**
     * @var RedirectInterface $redirect
     */
    protected RedirectInterface $redirect;
    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    protected ScopeConfigInterface $scopeConfig;
    /**
     * @var CustomerSession $customerSession
     */
    protected CustomerSession $customerSession;
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var DniCollection
     */
    protected DniCollection $dniCollection;
    /**
     * @var DomainCollection
     */
    protected DomainCollection $domainCollection;
    /**
     * @var CustomerCollection
     */
    protected CustomerCollection $customerCollection;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * CreatePost constructor.
     * @param RedirectFactory $redirectFactory
     * @param RedirectInterface $redirectInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSession $customerSession
     * @param ManagerInterface $messageManager
     * @param DniCollectionFactory $dniCollectionFactory
     * @param DomainCollectionFactory $domainCollectionFactory
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        RedirectFactory $redirectFactory,
        RedirectInterface $redirectInterface,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        DniCollectionFactory $dniCollectionFactory,
        DomainCollectionFactory $domainCollectionFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->resultRedirectFactory = $redirectFactory;
        $this->redirect = $redirectInterface;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->dniCollection = $dniCollectionFactory->create();
        $this->domainCollection = $domainCollectionFactory->create();
        $this->customerCollection = $customerCollectionFactory->create();
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param Closure $proceed
     * @return mixed
     */
    public function aroundExecute(\Magento\Customer\Controller\Account\CreatePost $subject, Closure $proceed)
    {
        if ($subject->getRequest()->isPost() && $this->isDniMethod($subject->getRequest())) {
            $dni = $subject->getRequest()->getPost('document_id');
            $email = $subject->getRequest()->getPost('email');
            $isDni = $subject->getRequest()->getPost('is_dni');
            $companyId = $subject->getRequest()->getPost('company_id');
            $domain = explode("@", $email);
            $domain = $domain[1];

            if ('true' == $isDni) {
                if ($this->isDniValid($dni, $companyId)) {
                    $this->customerSession->setIsPreApproved(true);
                    //somehow not getting unset in app/code/BalloonGroup/NewLogin/Plugin/Customer/Account/CreatePost.php:146
                } else {
                    $this->customerSession->setIsPreApproved(false);
                }
            } else {
                if ($this->isEmailValid($domain, $companyId)) {
                    $this->customerSession->setIsPreApproved(true);
                } else {
                    $this->customerSession->setIsPreApproved(false);

                }
            }
        }

        return $proceed();
    }

    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     * @param $result
     * @return ResultRedirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(\Magento\Customer\Controller\Account\CreatePost $subject, $result): ResultRedirect
    {
        if ($subject->getRequest()->isPost() && $this->isDniMethod($subject->getRequest())) {
            $existingMailError = false;

            $pendingApproval = !$this->customerSession->getIsPreApproved();
            $this->customerSession->unsIsPreApproved();
            $result = $this->resultRedirectFactory->create();
            $email = $subject->getRequest()->getParam('email');
            $this->customerSession->setRegisterEmail($email);
            $messageCollection = $this->messageManager->getMessages();

            if (!empty($messageCollection->getErrors())) {

                foreach ($messageCollection->getErrors() as $error) {

                    $identifier = $error->getIdentifier();

                    if  (self::EXISTING_CUSTOMER_ERROR == $identifier) {
                        $messageCollection->deleteMessageByIdentifier($identifier);
                        $existingMailError = true;
                    }
                }

                ($existingMailError)
                    ? $result->setPath('newlogin/register/existente')
                    : $result->setPath('customer/account/create');

                return $result;
            }

            if ($pendingApproval) {
                $result->setPath('newlogin/register/error');
            } else {
                $result->setPath('newlogin/register/success');
            }
        }

        return $result;
    }

    /**
     * @param $request
     * @return bool
     */
    protected function isDniMethod($request): ?bool
    {
        return $request->getPost('is_dni');
    }

    /**
     * @param $dni
     * @param $companyId
     * @return bool
     */
    protected function isDniValid($dni, $companyId): bool
    {
        $result = true;
        $dniModel = $this->dniCollection->addFieldToFilter("authorized_dni", $dni)->getFirstItem();

        if (!$dniModel->getId()) {
            $result = false;
        }

        if ($result === true && $dniModel->getCompanyId() != $companyId) {
            $result = false;
        }

        $existingCustomer = $this->customerCollection
            ->addFieldToFilter("document_id", $dni)
            ->addFieldToFilter("website_id", $this->storeManager->getWebsite()->getId())
            ->getFirstItem();

        if ($result === true && $existingCustomer->getId()) {
           $this->messageManager->addComplexErrorMessage(
               self::EXISTING_CUSTOMER_ERROR
           );
        }

        return $result;
    }

    /**
     * @param EmailDomain $email
     * @param $companyId
     * @return bool
     */
    protected function isEmailValid($email, $companyId): bool
    {
        $result = true;
        $emailModel = $this->domainCollection->addFieldToFilter("email_domain", $email)->getFirstItem();

        if (!$emailModel->getId()) {
            $result = false;
        }

        if ($result === true &&  $emailModel->getCompanyId() != $companyId) {
            $result = false;
        }

        return $result;
    }

}

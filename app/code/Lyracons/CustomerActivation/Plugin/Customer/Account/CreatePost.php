<?php
/**
 * Created by PhpStorm.
 * User: jgimenez
 * Date: 18/01/2018
 * Time: 12:39
 */

namespace Lyracons\CustomerActivation\Plugin\Customer\Account;

use Magento\Customer\Controller\Account\CreatePost as CreatePostAction;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Lyracons\CustomerActivation\Helper\Data as HelperData;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class CreatePost
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
     * CreatePost constructor.
     * @param HelperData $helperData
     * @param RedirectFactory $redirectFactory
     * @param RedirectInterface $redirectInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSession $customerSession
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        HelperData $helperData,
        RedirectFactory $redirectFactory,
        RedirectInterface $redirectInterface,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        MessageManagerInterface $messageManager
    )
    {
        $this->resultRedirectFactory = $redirectFactory;
        $this->redirect = $redirectInterface;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
    }

    /**
     * @param CreatePost $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute($subject, $result)
    {
        if ( !$this->helperData->isModuleActive() ) {
            return $result;
        }

        $lastCustomerId = $this->customerSession->getCustomerId();
        if ($lastCustomerId) {
            $this->messageManager->getMessages(true);
            $this->customerSession->logout()->setBeforeAuthUrl($this->redirect->getRefererUrl())
                ->setLastCustomerId($lastCustomerId);

            $this->messageManager->addSuccessMessage(__('Your account will be enabled by one administrator'));

            /** @var ResultRedirect $result */
            $result = $this->resultRedirectFactory->create();
            $result->setPath($this->helperData->getSuccessRedirectPath());
        }

        return $result;
    }

}

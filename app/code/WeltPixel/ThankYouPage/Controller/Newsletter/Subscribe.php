<?php
namespace WeltPixel\ThankYouPage\Controller\Newsletter;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Subscribe extends \Magento\Newsletter\Controller\Subscriber\NewAction
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var SubscriptionManagerInterface
     */
    protected $subscriptionManager;

    /**
     * Subscribe constructor.
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $customerAccountManagement
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param JsonFactory $resultJsonFactory
     * @param EmailValidator|null $emailValidator
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $customerAccountManagement,
        SubscriptionManagerInterface $subscriptionManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        JsonFactory $resultJsonFactory,
        EmailValidator $emailValidator = null
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->subscriptionManager = $subscriptionManager;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl,
            $customerAccountManagement,
            $subscriptionManager,
            $emailValidator
        );
    }

    /**
     * @param string $email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateEmailAvailable($email)
    {
        /**
         * Original function rewritten
         * Allow the subscription for user on checkout success page for the same email address
         */
        $orderEmail = $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
        if ($orderEmail == $email) {
            return true;
        }
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId)
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }

    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::execute();
        }

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $email = (string)$this->getRequest()->getPost('email');
            $sucessMessage = '';
            try {
                $this->validateEmailFormat($email);
                $this->validateGuestSubscription();
                $this->validateEmailAvailable($email);

                $websiteId = (int)$this->_storeManager->getStore()->getWebsiteId();
                $subscriber = $this->_subscriberFactory->create()->loadBySubscriberEmail($email, $websiteId);
                if ($subscriber->getId()
                    && (int)$subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
                ) {
                    return $this->resultJsonFactory->create()->setData(
                        [
                            'errors' => true,
                            'message' => __('This email address is already subscribed.')
                        ]
                    );
                }

                $storeId = (int)$this->_storeManager->getStore()->getId();
                $currentCustomerId = $this->getSessionCustomerId($email);
                $subscriber = $currentCustomerId
                    ? $this->subscriptionManager->subscribeCustomer($currentCustomerId, $storeId)
                    : $this->subscriptionManager->subscribe($email, $storeId);
                $status = (int)$subscriber->getSubscriberStatus();
                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                    $sucessMessage = __('The confirmation request has been sent.');
                } else {
                    $sucessMessage = __('Thank you for your subscription.');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return $this->resultJsonFactory->create()->setData(
                    [
                        'errors' => true,
                        'message' => __('There was a problem with the subscription: %1', $e->getMessage())
                    ]
                );
            } catch (\Exception $e) {
                return $this->resultJsonFactory->create()->setData(
                    [
                        'errors' => true,
                        'message' => __('Something went wrong with the subscription.')
                    ]
                );
            }

            return $this->resultJsonFactory->create()->setData(
                [
                    'errors' => false,
                    'message' => $sucessMessage
                ]
            );
        }
    }

    /**
     * Get customer id from session if he is owner of the email
     *
     * @param string $email
     * @return int|null
     */
    private function getSessionCustomerId(string $email)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return null;
        }

        $customer = $this->_customerSession->getCustomerDataObject();
        if ($customer->getEmail() !== $email) {
            return null;
        }

        return (int)$this->_customerSession->getId();
    }
}

<?php

namespace BalloonGroup\ModalNewsletter\Plugin\Controller\Subscriber;

use Exception;
use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Validator\EmailAddress as EmailValidator;

class NewAction
{
    /**
     * @var CustomerAccountManagement
     */
    protected $customerAccountManagement;

    /**
     * @var $resultJsonFactory JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var EmailValidator|null
     */
    private $emailValidator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * Initialize dependencies.
     *
     * @param CustomerAccountManagement $customerAccountManagement
     * @param SubscriberFactory $subscriberFactory
     * @param JsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerUrl $customerUrl
     * @param EmailValidator|null $emailValidator
     */

    public function __construct(
        CustomerAccountManagement $customerAccountManagement,
        SubscriberFactory         $subscriberFactory,
        JsonFactory               $resultJsonFactory,
        StoreManagerInterface     $storeManager,
        Session                   $customerSession,
        ScopeConfigInterface      $scopeConfig,
        CustomerUrl               $customerUrl,
        EmailValidator            $emailValidator
    )
    {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->emailValidator = $emailValidator;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->customerUrl = $customerUrl;
    }

    /**
     * Retrieve available Order fields list
     *
     * @param \Magento\Newsletter\Controller\Subscriber\NewAction $subject
     * @param $procede
     * @return Json
     */
    public function aroundExecute($subject, $procede)
    {
        $request = $subject->getRequest();
        if (!$request->isXmlHttpRequest()) {
            return $procede();
        } else {
            $response = [
                'status' => 0,
                'msg' => __('Algo salió mal con la suscripción.'),
            ];

            if ($request->isPost() && $request->getPost('email')) {
                $email = (string)$request->getPost('email');

                try {
                    $this->validateEmailFormat($email);
                    $this->validateGuestSubscription();
                    $this->validateEmailAvailable($email);

                    $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
                    /** @var Subscriber $subscriber */

                    $subscriber = $this->subscriberFactory->create()->loadBySubscriberEmail($email, $websiteId);
                    if ($subscriber->getId()
                        && (int)$subscriber->getSubscriberStatus() === Subscriber::STATUS_SUBSCRIBED) {
                        $response = [
                            'status' => 0,
                            'msg' => __('Esta dirección de correo electrónico ya está suscrita.')
                        ];
                        return $this->resultJsonFactory->create()->setData($response);

                    }

                    $status = $this->subscriberFactory->create()->subscribe($email);
                    if ($status == Subscriber::STATUS_NOT_ACTIVE) {
                        $response = [
                            'status' => 1,
                            'msg' => __('La solicitud de confirmación ha sido enviada.'),
                        ];
                    } else {
                        $response = [
                            'status' => 1,
                            'msg' => __('Gracias por su suscripción.'),
                        ];
                    }
                } catch (LocalizedException $e) {
                    $response = [
                        'status' => 0,
                        'msg' => __('Hubo un problema con la suscripción.: %1', $e->getMessage()),
                    ];
                } catch (Exception $e) {
                    $response = [
                        'status' => 0,
                        'msg' => __('Algo salió mal con la suscripción.'),
                    ];
                }
            }

            return $this->resultJsonFactory->create()->setData($response);
        }
    }

    /**
     * Validates the format of the email address
     *
     * @param string $email
     * @return void
     * @throws LocalizedException
     */
    protected function validateEmailFormat($email)
    {
        if (!$this->emailValidator->isValid($email)) {
            throw new LocalizedException(__('Por favor, introduce una dirección de correo electrónico válida.'));
        }
    }

    /**
     * Validates that the email address isn't being used by a different account.
     *
     * @param string $email
     * @return void
     * @throws LocalizedException
     */
    protected function validateEmailAvailable($email)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        if ($this->customerSession->isLoggedIn()
            && ($this->customerSession->getCustomerDataObject()->getEmail() !== $email
                && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId))
        ) {
            throw new LocalizedException(
                __('Esta dirección de correo electrónico ya está suscrita.')
            );
        }
    }

    /**
     * Validates that if the current user is a guest, that they can subscribe to a newsletter.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function validateGuestSubscription()
    {
        if ($this->scopeConfig->getValue(
                Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
                ScopeInterface::SCOPE_STORE
            ) != 1 && !$this->customerSession->isLoggedIn()
        ) {
            throw new LocalizedException(
                __(
                    'Lo sentimos, pero el administrador denegó la suscripción a los invitados. Por favor <a href="%1">register</a>.',
                    $this->customerUrl->getRegisterUrl()
                )
            );
        }
    }
}

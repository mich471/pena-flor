<?php

namespace Lyracons\CustomerActivation\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\MailException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use Magento\Customer\Api\Data\CustomerInterface;
use Lyracons\CustomerActivation\Helper\Data as HelperData;

class Email
{

    /**
     * @var State $state
     */
    protected $state;

    /**
     * @var TransportBuilder $transportBuilder
     */
    protected $transportBuilder;

    /**
     * @var string
         */
    protected $xmlConfigPathTemplate;

    /**
     * @var StoreInterface
         */
    protected $store;

    /**
     * @var string
         */
    protected $areaTemplate;

    /**
     * @var string
         */
    protected $bCc;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param State $state
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        State $state,
        TransportBuilder $transportBuilder
    )
    {
        $this->state = $state;
        $this->transportBuilder = $transportBuilder;
        $this->store = null;
        $this->areaTemplate = Area::AREA_FRONTEND;
        $this->bCc = null;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPathTemplate($path)
    {
        $this->xmlConfigPathTemplate = $path;
        return $this;
    }

    /**
     * @param string $bCc
     * @return $this
     */
    public function setBcc($bCc)
    {
        $this->bCc = $bCc;
        return $this;
    }

    /**
     * @param StoreInterface $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @param CustomerInterface $customer
     * @return $this
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendEmailToCustomer($customer)
    {
        $template = $this->helperData->getCustomerEmailTemplatePath();
        $store = $this->storeManager->getStore($customer->getStoreId());
        $this->setStore($store)
            ->setPathTemplate($template)
            ->sendEmail(
                [
                    'customer' => $customer
                ],
                $this->getGeneralSender(),
                [
                    'name'  => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    'email' => $customer->getEmail()
                ]
            );
        return $this;
    }

    /**
     * @param CustomerInterface $customer
     * @param $email
     * @return $this
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendEmailToAdministrator($customer, $email)
    {
        $template = $this->helperData->getAdminEmailTemplatePath();
        $store = $this->storeManager->getStore($customer->getStoreId());
        $this->setStore($store)
            ->setPathTemplate($template)
            ->sendEmail(
                [
                    'customer' => $customer
                ],
                $this->getGeneralSender(),
                [
                    'name'  => 'Admin',
                    'email' => $email
                ]
            );

        return $this;
    }

    /**
     * @param $vars
     * @param $senderInfo
     * @param $receiverInfo
     * @return $this
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendEmail($vars, $senderInfo, $receiverInfo)
    {
        $this->generateTemplate($vars, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        return $this;
    }

    /**
     * @param $emailTemplateVariables
     * @param $senderInfo
     * @param $receiverInfo
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->transportBuilder->setTemplateIdentifier($this->getTemplate($this->xmlConfigPathTemplate))
            ->setTemplateOptions(
                [
                    'area'  => $this->areaTemplate,
                    'store' => $this->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo, $this->getStore()->getCode())
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        if (!is_null($this->bCc)) {
            $this->transportBuilder->addBcc($this->bCc);
        }

        return $this;
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getStore()
    {
        if (is_null($this->store)) {
            $this->store = $this->storeManager->getStore();
        }
        return $this->store;
    }

    /**
     * @param $xmlPath
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getTemplate($xmlPath)
    {
        return $this->helperData->getConfigValue($xmlPath, $this->getStore()->getId());
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getGeneralSender()
    {
        return [
            'name'  => $this->helperData->getConfigValue('trans_email/ident_general/name', $this->getStore()->getId()),
            'email' => $this->helperData->getConfigValue('trans_email/ident_general/email', $this->getStore()->getId()),
        ];
    }
}

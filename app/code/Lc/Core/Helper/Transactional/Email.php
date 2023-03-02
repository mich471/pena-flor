<?php

namespace Lc\Core\Helper\Transactional;

use Lc\Core\Helper\Data as HelperData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;

class Email extends HelperData
{
    const XML_CONFIG_PATH_EMAIL_TEMPLATE = 'section/group/your_email_template_field_id';

    /**
     * @var StateInterface $inlineTranslation
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder $_transportBuilder
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
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ){
        parent::__construct($context, $storeManager);
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->xmlConfigPathTemplate = self::XML_CONFIG_PATH_EMAIL_TEMPLATE;
        $this->store = null;
        $this->areaTemplate = Area::AREA_FRONTEND;
        $this->bCc = null;
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
     * @param $vars
     * @param $senderInfo
     * @param $receiverInfo
     * @return $this
     * @throws MailException
     * @throws LocalizedException
     */
    public function sendEmail($vars, $senderInfo, $receiverInfo)
    {
        $this->generateTemplate($vars,$senderInfo,$receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        return $this;
    }

    /**
     * @param $emailTemplateVariables
     * @param $senderInfo
     * @param $receiverInfo
     * @return $this
     * @throws MailException
     * @throws NoSuchEntityException
     */
    protected function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $scopeId = $this->getStore()->getId();

        $this->transportBuilder->setTemplateIdentifier($this->getTemplate($this->xmlConfigPathTemplate))
            ->setTemplateOptions(
                [
                    'area' => $this->areaTemplate,
                    'store' => $scopeId,
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFromByScope($senderInfo, $scopeId)
            ->addTo($receiverInfo['email'],$receiverInfo['name'])
        ;

        if ( !is_null($this->bCc) ) {
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
        if ( is_null($this->store) ) {
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
        return $this->getConfigValue($xmlPath, $this->getStore()->getId());
    }
}

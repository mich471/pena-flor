<?php

namespace BalloonGroup\NewLogin\Controller\EmailDomain;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain\Collection as EmailCollection;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;

class Company extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var EmailCollection
     */
    protected EmailCollection $emailCollection;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param EmailCollection $emailCollection
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        EmailCollection $emailCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->emailCollection = $emailCollection;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax())
        {
            $companyId = $this->getRequest()->getParam('id');
            $companyDomains = $this->getCompanyDomains($companyId);
            $result->setData($companyDomains);
        }

        return $result;
    }

    /**
     * @param int $companyId
     * @return array
     */
    private function getCompanyDomains(int $companyId): array
    {
        $items = $this->emailCollection->addFieldToFilter(
            'company_id',
            ['eq' => $companyId]
        )->load();

        $emailDomains = [];
        foreach($items as $item) {
            $domain = $item->getData('email_domain');
            $emailDomains[] = $domain;
        }

        return $emailDomains;
    }
}

<?php

namespace BalloonGroup\NewLogin\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory;

class Agreements implements ArgumentInterface
{

    /**
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $agreementsCollection;

    /**
     * @param CollectionFactory $agreementsCollection
     */
    public function __construct(
        CollectionFactory $agreementsCollection
    )
    {
        $this->agreementsCollection = $agreementsCollection;
    }

    /**
     * @return array
     */
    public function getAgreements()
    {
        $agreementsCollection = $this->agreementsCollection->create();
        $agreementMappedArray = [];
        foreach ($agreementsCollection as $agreement) {
            if ($agreement->getIsActive()) {
                $agreementMappedArray = [
                    'mode' => $agreement->getMode(),
                    'agreementId' => $agreement->getAgreementId(),
                    'checkboxText' => $agreement->getCheckboxText(),
                    'content' => $agreement->getContent()
                ];
            }
        }
        $agreementJson = json_encode($agreementMappedArray);

        return $agreementMappedArray;
    }
}

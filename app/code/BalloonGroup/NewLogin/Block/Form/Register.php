<?php
namespace BalloonGroup\NewLogin\Block\Form;

use Magento\Customer\Block\Form\Register as RegisterBlock;
use Magento\Framework\App\ObjectManager;
use BalloonGroup\NewLogin\Model\ResourceModel\Company\Collection as CompanyCollection;

class Register extends RegisterBlock
{
    /**
     * @return string
     */
    public function getJsLayout(): string
    {
        $companies = $this->getCompanies();
        $this->jsLayout['components']['newlogin-register-form']['config']['companiesData'] = $companies;
        //$this->jsLayout['components']['newlogin-register-form']['children']['company-select']['config']['companiesData'] = $companies;
        return parent::getJsLayout();
    }

    /**
     * @return string
     */
    protected function getCompanies(): string
    {
        $companyCollection = ObjectManager::getInstance()->get(CompanyCollection::class);
        $items = $companyCollection->getItems();

        $companies = [];
        foreach($items as $company) {
            $companyId = $company->getId();
            $companyName = $company->getCompany();
            array_push($companies, ["value" => $companyId, "label" => $companyName]);
        }

        return json_encode($companies);
    }
}

<?php
namespace BalloonGroup\NewLogin\Block\Adminhtml\System\Company;

use BalloonGroup\NewLogin\Model\ResourceModel\Company\CollectionFactory as CompanyFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Data\OptionSourceInterface;

class SelectCompany extends DataObject implements OptionSourceInterface
{   
    /**
     * @var CompanyFactory
     */
    protected $_companyFactory;

    /**
     * Constructor
     *
     * @param CompanyFactory $companyFactory
     * @param array $data
     */
    public function __construct(
        CompanyFactory $companyFactory,
        array $data = []
    )
    {
        $this->_companyFactory    = $companyFactory;
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getArray();
    }

    /**
     * @return array
     */
    public function getArray()
    {
        $companyCollection = $this->_companyFactory->create()->getData();
        $arr = [];
        foreach ($companyCollection as $company)
        {
            $arr[] = ['label' => __($company['company']) ,'value' => $company['company_id']];
        }
        return $arr;
    }
}
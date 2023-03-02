<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use BalloonGroup\NewLogin\Model\Company;
use BalloonGroup\NewLogin\Model\ResourceModel\Company as CompanyResourceModel;
use BalloonGroup\NewLogin\Model\ResourceModel\Company\CollectionFactory as CompanyCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use BalloonGroup\General\Logger\Logger;
use BalloonGroup\NewLogin\Model\Dni;
use BalloonGroup\NewLogin\Model\ResourceModel\Dni as DniResourceModel;
use BalloonGroup\NewLogin\Model\ResourceModel\Dni\CollectionFactory as DniCollectionFactory;
use BalloonGroup\NewLogin\Model\EmailDomain;
use BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain  as EmailDomainResourceModel;
use BalloonGroup\NewLogin\Model\ResourceModel\EmailDomain\CollectionFactory as DomainCollectionFactory;

class Import extends Action
{

    const ENTITY_TYPE_COMPANY = 'company';
    const ENTITY_TYPE_DNI = 'dni';
    const ENTITY_TYPE_DOMAIN = 'domain';

    /**
     * @var Csv
     */
    protected Csv $csv;
    /**
     * @var Company
     */
    protected Company $companyModel;
    /**
     * @var CompanyCollectionFactory
     */
    protected CompanyCollectionFactory $companyCollectionFactory;
    /**
     * @var CompanyResourceModel
     */
    protected CompanyResourceModel $companyResourceModel;
    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;
    /**
     * @var Logger
     */
    protected Logger $logger;
    /**
     * @var DniCollectionFactory
     */
    protected DniCollectionFactory $dniCollectionFactory;
    /**
     * @var Dni
     */
    protected Dni $dniModel;
    /**
     * @var DniResourceModel
     */
    protected DniResourceModel $dniResourceModel;
    /**
     * @var EmailDomain
     */
    protected EmailDomain $domainModel;
    /**
     * @var EmailDomainResourceModel
     */
    protected EmailDomainResourceModel $domainResourceModel;
    /**
     * @var DomainCollectionFactory
     */
    protected DomainCollectionFactory $domainCollectionFactory;
    /**
     * @var array
     */
    protected array $companiesDict;
    /**
     * @var array|null
     */
    protected array $companies;
    /**
     * @var array|null
     */
    protected array $dnis;
    /**
     * @var array|null
     */
    protected array $domains;

    /**
     * @param Context $context
     * @param Csv $csv
     * @param Company $companyModel
     * @param DirectoryList $directoryList
     * @param CompanyCollectionFactory $companyCollectionFactory
     * @param CompanyResourceModel $companyResourceModel
     * @param Logger $logger
     * @param Dni $dniModel
     * @param DniResourceModel $dniResourceModel
     * @param DniCollectionFactory $dniCollectionFactory
     * @param EmailDomain $domainModel
     * @param EmailDomainResourceModel $domainResourceModel
     * @param DomainCollectionFactory $domainCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        Csv $csv,
        Company $companyModel,
        DirectoryList $directoryList,
        CompanyCollectionFactory $companyCollectionFactory,
        CompanyResourceModel $companyResourceModel,
        Logger $logger,
        Dni $dniModel,
        DniResourceModel $dniResourceModel,
        DniCollectionFactory $dniCollectionFactory,
        EmailDomain $domainModel,
        EmailDomainResourceModel $domainResourceModel,
        DomainCollectionFactory $domainCollectionFactory
    ) {
        $this->csv = $csv;
        $this->companyModel = $companyModel;
        $this->companyCollectionFactory = $companyCollectionFactory;
        $this->companyResourceModel = $companyResourceModel;
        $this->directoryList = $directoryList;
        $this->logger = $logger;
        $this->dniModel = $dniModel;
        $this->dniResourceModel = $dniResourceModel;
        $this->dniCollectionFactory = $dniCollectionFactory;
        $this->domainModel = $domainModel;
        $this->domainResourceModel = $domainResourceModel;
        $this->domainCollectionFactory = $domainCollectionFactory;
        $this->companiesDict = [];
        $this->dnis = $dniCollectionFactory->create()->getData();
        $this->domains = $domainCollectionFactory->create()->getData();
        $this->companies = $companyCollectionFactory->create()->getData();
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        ini_set('max_execution_time', '100');
        $resultRedirect = $this->resultRedirectFactory->create();


        $tmpDir = $this->directoryList->getPath('tmp');
        $file = $tmpDir . '/datasheet-companiesInfo.csv';

        if (!isset($file)) {
            $this->messageManager->addWarningMessage('Please upload valid CSV file.');
            return $resultRedirect->setPath('newlogin/import/index');
        }

        $csv = $this->csv;
        $csv->setDelimiter(',');
        $csvData = $csv->getData($file);

        if (!$this->isFirstRowValid(array_shift($csvData))) {
            $this->messageManager->addWarningMessage('El csv debe tener la siguiente estructura: Empresa,Dni,Dominio.');
            return $resultRedirect->setPath('newlogin/import/index');
        }

        $duplicatedDnis = 0;
        $duplicatedDomains = 0;
        $imported = 0;
        foreach ($csvData as $row => $data) {
            $this->logger->print(print_r($data, true));

            $company = $data[0];
            $dni = isset($data[1]) ? $data[1] : '';
            $domain = isset($data[2]) ? $data[2] : '';

            $this->saveCompany($company);
            $companyId = $this->getCompanyId($company);

            if ($dni != '') {
                $this->saveDni($companyId, $dni, $duplicatedDnis);
            }
            if ($domain != '') {
                $this->saveDomain($companyId, $domain, $duplicatedDomains);
            }

            $imported++;
        }
        if($imported)
            $this->messageManager->addSuccessMessage($imported.' record(s) imported successfully!');
        if($duplicatedDnis)
            $this->messageManager->addWarningMessage($duplicatedDnis .' dni(s) not imported because they already exist.');
        if($duplicatedDomains)
            $this->messageManager->addWarningMessage($duplicatedDomains .' domain(s) not imported because they already exist.');

        return $resultRedirect->setPath('newlogin/import/index');
    }

    /**
     * @param $value
     * @param $collection
     * @param $field
     * @return bool
     */
    protected function recordAlreadyExists($value, $collection, $field): bool
    {
        foreach ($collection as $key => $member) {
            if ($member[$field] == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @return void
     * @throws AlreadyExistsException
     */
    protected function saveCompany($value)
    {
        if (!$this->recordAlreadyExists($value, $this->companies, 'company')) {
            $this->companyModel->setData([
                'company' => $value,
            ]);
            $this->companyResourceModel->save($this->companyModel);
            $this->refreshCollection(self::ENTITY_TYPE_COMPANY);
        }
    }

    /**
     * @param $companyId
     * @param $value
     * @param $duplicated
     * @return void
     * @throws AlreadyExistsException
     */
    protected function saveDni($companyId, $value, &$duplicated)
    {
        if (!$this->recordAlreadyExists($value, $this->dnis, 'authorized_dni')) {
            $this->dniModel->setData([
                'company_id' => $companyId,
                'authorized_dni' => $value,
            ]);
            $this->dniResourceModel->save($this->dniModel);
            $this->refreshCollection(self::ENTITY_TYPE_DNI);
        } else {
            $duplicated++;
        }
    }

    /**
     * @param $companyId
     * @param $value
     * @param $duplicated
     * @return void
     * @throws AlreadyExistsException
     */
    protected function saveDomain($companyId, $value, &$duplicated)
    {
        $value = ($value[0] == '@') ? substr($value, 1) : $value;
        if (!$this->recordAlreadyExists($value, $this->domains, 'email_domain')) {
            $this->domainModel->setData([
                'company_id' => $companyId,
                'email_domain' => $value,
            ]);
            $this->domainResourceModel->save($this->domainModel);
            $this->refreshCollection(self::ENTITY_TYPE_DOMAIN);
        } else {
            $duplicated++;
        }
    }

    /**
     * @param $companyName
     * @return mixed
     */
    protected function getCompanyId($companyName)
    {
        if (isset($this->companiesDict[$companyName])) {
            return $this->companiesDict[$companyName];
        }

        $this->companyResourceModel->load($this->companyModel, $companyName, 'company');
        $companyId = $this->companyModel->getId();
        $this->companiesDict[$companyName] = $companyId;

        return $companyId;
    }

    /**
     * @param $row
     * @return bool
     */
    protected function isFirstRowValid($row) {
        if (isset($row[0]) && strtolower($row[0]) !== 'empresa') return false;
        if (isset($row[1]) && strtolower($row[1]) !== 'dni') return false;
        if (isset($row[2]) && strtolower($row[2]) !== 'dominio') return false;

        return true;
    }

    /**
     * @param string $entity
     * @return void
     */
    private function refreshCollection(string $entity)
    {
        switch ($entity) {
            case self::ENTITY_TYPE_COMPANY :
                $this->companies = $this->companyCollectionFactory->create()->getData();
                break;
            case self::ENTITY_TYPE_DNI :
                $this->dnis = $this->dniCollectionFactory->create()->getData();
                break;
            case self::ENTITY_TYPE_DOMAIN :
                $this->domains = $this->domainCollectionFactory->create()->getData();
                break;
        }
    }

}

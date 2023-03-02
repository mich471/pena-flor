<?php

namespace Lyracons\Icommkt\Observer;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Lyracons\Icommkt\Logger\Logger;
use Magento\Framework\ObjectManagerInterface;
use Lyracons\Icommkt\Helper\ContactSync;

/**
 * Class ManageCustomer
 * @package Lyracons\Icommkt\Observer
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @todo fix PHP mess-detector warning by renaming property
 */
class ManageCustomer  implements \Magento\Framework\Event\ObserverInterface
{

    protected $logger;
    protected $directory_list;
    protected $objectManager;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    protected $helper;

    /**
     * ManageCustomer constructor.
     * @param Logger $logger
     * @param DirectoryList $directory_list
     * @param ObjectManagerInterface $interface
     * @param CustomerRepositoryInterface $repository
     * @param ContactSync $contactSync
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @todo fix PHP mess-detector warnings
     */
    public function __construct(
        Logger $logger,
        DirectoryList $directory_list,
        ObjectManagerInterface $interface,
        CustomerRepositoryInterface $repository,
        ContactSync $contactSync
    )
    {
        $this->logger = $logger;
        $this->directory_list = $directory_list;
        $this->objectManager = $interface;
        $this->customerRepository = $repository;
        $this->helper = $contactSync;
        $this->logger->setEnabled($this->helper->isLogEnabled())
            ->setVerboseMode($this->helper->isLogVerbose());
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @todo fix PHP mess-detector warning by renaming variable
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getData('customer_data_object');
        /** @var \Magento\Customer\Model\Customer $_customer */
        $_customer = $this->objectManager->get('Magento\Customer\Model\Customer')->load($customer->getId());
        if ($this->helper->isEnabled($_customer->getWebsiteId())) {
            $this->logger->debug('notify to icommkt: ' . $_customer->getEmail());
            $this->sendToIcommkt($_customer);
        }
    }

    /**
     * @param Customer $customer
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @todo fix PHP mess-detector warning
     */
    public function sendToIcommkt($customer)
    {
        $websiteId = $customer->getWebsiteId();
        $apiKey = $this->helper->getApiKey($websiteId);
        $stringData = $this->buildJsonData($customer, $websiteId);
        $data = json_encode($stringData);
        $this->logger->verbose('send to icommkt: ' . "\n" . $data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->helper->getApiUrl($websiteId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'Authorization: ' . $apiKey . ':0',
                'Access-Control-Allow-Origin: *')
        );

        $result = curl_exec($ch);
        curl_close($ch);
//        $resultobj = json_decode($result);
    }

    /**
     * Build Object to send data
     *
     * @param Customer $customer
     * @param int $websiteId
     * @return array
     */
    private function buildJsonData($customer, $websiteId)
    {
        $attrs = $this->helper->getAttributeCodes();;
        $arrayData = array(
            'ProfileKey' => $this->helper->getProfileKey($websiteId),
            'Contact' => array(
                'Email' => '',
                'CustomFields' => array()
            )
        );

        foreach ($attrs as $code) {
            $value = $customer->getData($code);
            $icommColumn = $this->helper->getIcommColumn($code);
            if ($code == "dob") {
                try {
                    $dob = new \DateTime($value);
                    $value = $dob->format('d/m/Y');
                } catch (Exception $e) {
                    // well, born on a 31st february maybe? do we really care?
                }
            }

            if ($icommColumn != "") {
                $arrayData['Contact']['CustomFields'][] = array('Key' => $icommColumn, 'Value' => $value);
            }
        }
        $arrayData['Contact']['Email'] = $customer->getData('email');

        return $arrayData;
    }

}

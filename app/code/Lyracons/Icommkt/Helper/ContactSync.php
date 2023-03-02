<?php

namespace Lyracons\Icommkt\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Lyracons\Icommkt\Logger\Logger;

class ContactSync extends AbstractHelper
{

    const XML_CONFIG_PATH_MODULE_ENABLED        = 'icommkt/general/enabled';
    const XML_CONFIG_PATH_MODULE_API_URL        = 'icommkt/general/apiurl';
    const XML_CONFIG_PATH_MODULE_API_KEY        = 'icommkt/general/apikey';
    const XML_CONFIG_PATH_MODULE_PROFILE_KEY    = 'icommkt/general/profilekey';
    const XML_CONFIG_PATH_MODULE_LOG_ENABLED    = 'icommkt/general/log';
//    const XML_CONFIG_PATH_MODULE_LOG_FILE       = 'icommkt/general/logfile';
    const XML_CONFIG_PATH_MODULE_VERBOSE_LOG    = 'icommkt/general/verbose';

    protected $icommColumns = [
        'firstname' => 'nombre',
        'lastname'  => 'apellido',
        'dob'       => 'fecha_nacimiento'
    ];

    /**
     * @var Reader $reader
     */
    protected $reader;

    /**
     * @var string $apiUrl
     */
    protected $apiUrl;

    /**
     * @var string $templateFile
     */
    protected $templateFile;

    /**
     * @var array $template
     */
    protected $template;

    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * ContactSync constructor.
     * @param Context $context
     * @param Reader $reader
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        Reader $reader,
        Logger $logger
    )
    {
        parent::__construct($context);
        $this->reader = $reader;
        $this->logger = $logger;
        $this->logger->setEnabled($this->isLogEnabled())
            ->setVerboseMode($this->isLogVerbose());
    }

    /**
     * @param $email
     * @return $this
     */
    public function send($email)
    {
        if (!is_null($email)) {
            $this->notifyToIcommkt($email);
        }
        return $this;
    }

    /**
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabled($websiteId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_CONFIG_PATH_MODULE_ENABLED, $websiteId);
    }

    /**
     * @param $email
     * @param int|null $websiteId
     * @return $this
     */
    public function notifyToIcommkt($email, $websiteId = null)
    {
        $this->logger->debug(__METHOD__);
        $payload = $this->preparePayload($email);
        $this->logger->verbose('payload: ' . var_export($payload, true));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl($websiteId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
                'Authorization: ' . $this->getApiKey($websiteId) . ':0',
                'Access-Control-Allow-Origin: *']
        );

        $result = curl_exec($ch);
        curl_close($ch);
        $this->logger->debug("RESPONSE FROM ICOMMKT: " . $result);
        return $this;
    }

    /**
     * @param int|null $websiteId
     * @return string
     */
    public function getApiUrl($websiteId = null): string
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_API_URL, $websiteId) ?: '';
    }

    /**
     * @return string
     */
    public function getConfig(): string
    {
        return $this->templateFile;
    }

    /**
     * @param int|null $websiteId
     * @return string
     */
    public function getApiKey($websiteId = null): string
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_API_KEY, $websiteId) . ':user';
    }

    /**
     * @param int|null $websiteId
     * @return string
     */
    public function getProfileKey($websiteId = null): string
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_PROFILE_KEY, $websiteId) ?: '';
    }

    /**
     * @param int|null $websiteId
     * @return string
     * @todo
     * /
    public function getLogFile($websiteId = null): string
    {
        return $this->getConfigValue(self::XML_CONFIG_PATH_MODULE_LOG_FILE, $websiteId);
    }
     */

    /**
     * @param int|null $websiteId
     * @return bool
     */
    public function isLogEnabled($websiteId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_CONFIG_PATH_MODULE_LOG_ENABLED, $websiteId);
    }

    /**
     * @param int|null $websiteId
     * @return bool
     */
    public function isLogVerbose($websiteId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_CONFIG_PATH_MODULE_VERBOSE_LOG, $websiteId);
    }

    /**
     * @param string $email
     * @param int|null $websiteId
     * @return false|string
     */
    protected function preparePayload($email, $websiteId = null): string
    {
        $profileKey = $this->getProfileKey($websiteId);
        return json_encode($this->buildData($email, $profileKey));
    }

    /**
     * @param string $email
     * @param string $profileKey
     * @return array
     */
    protected function buildData($email, $profileKey): array
    {
        return [
            'ProfileKey' => $profileKey,
            'Contact'    => [
                'Email'        => $email,
                'CustomFields' => []
            ]
        ];
    }

    /**
     * @param string $path
     * @param int|null $websiteId
     * @return mixed
     */
    public function getConfigValue($path, $websiteId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    public function getAttributeCodes()
    {
        return [
            'firstname', 'lastname', 'dob'
        ];
    }

    public function getIcommColumn($magentoColumn)
    {
        return isset($this->icommColumns[$magentoColumn])
            ? $this->icommColumns[$magentoColumn]
            : '';
    }
}

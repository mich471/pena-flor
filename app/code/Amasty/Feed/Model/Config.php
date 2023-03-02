<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model;

class Config
{
    /**#@+
     * Configuration paths
     */
    public const FEED_SECTION = 'amasty_feed/';

    public const GENERAL_GROUP = 'general/';

    public const MULTI_PROCESS_GROUP = 'multi_process/';

    public const NOTIFICATION_GROUP = 'notifications/';

    public const BATCH_SIZE_FIELD = 'batch_size';

    public const FILE_PATH_FIELD = 'file_path';

    public const STORAGE_FOLDER = 'storage_folder';

    public const ENABLED_FIELD = 'enabled';

    public const PROCESS_COUNT_FIELD = 'process_count';

    public const PREVIEW_ITEMS = 'preview_items';

    public const EVENTS_FIELD = 'events';

    public const SENDER_FIELD = 'email_sender';

    public const EMAILS_FIELD = 'emails';

    public const SUCCESS_TEMPLATE_FIELD = 'success_template';

    public const UNSUCCESS_TEMPLATE_FIELD = 'unsuccess_template';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $group
     * @param string $path
     *
     * @return mixed
     */
    private function getScopeValue($group, $path)
    {
        return $this->config->getValue(
            self::FEED_SECTION . $group . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $group
     * @param string $path
     *
     * @return bool
     */
    private function isSetFlag($group, $path)
    {
        return $this->config->isSetFlag(
            self::FEED_SECTION . $group . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getItemsForPreview()
    {
        return (int)$this->getScopeValue(self::GENERAL_GROUP, self::PREVIEW_ITEMS);
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return (int)$this->getScopeValue(self::GENERAL_GROUP, self::BATCH_SIZE_FIELD);
    }

    /**
     * @return string
     */
    public function getSelectedEvents()
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::EVENTS_FIELD);
    }

    /**
     * @return string
     */
    public function getSuccessEmailTemplate()
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::SUCCESS_TEMPLATE_FIELD);
    }

    /**
     * @return string
     */
    public function getUnsuccessEmailTemplate()
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::UNSUCCESS_TEMPLATE_FIELD);
    }

    /**
     * @return string
     */
    public function getEmailSenderContact()
    {
        return $this->getScopeValue(self::NOTIFICATION_GROUP, self::SENDER_FIELD);
    }

    /**
     * @return array|null
     * @throws \Zend_Validate_Exception
     */
    public function getEmails()
    {
        if ($emails = $this->getScopeValue(self::NOTIFICATION_GROUP, self::EMAILS_FIELD)) {
            $emails = array_map('trim', explode(',', $emails));

            foreach ($emails as $key => $email) {
                if (!\Zend_Validate::is($email, 'EmailAddress')) {
                    unset($emails[$key]);
                }
            }
        }

        return $emails;
    }

    /**
     * @return string
     */
    public function getStorageFolder()
    {
        return $this->getScopeValue(self::GENERAL_GROUP, self::STORAGE_FOLDER);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->getScopeValue(self::GENERAL_GROUP, self::FILE_PATH_FIELD);
    }

    /**
     * @return int
     */
    public function getMaxJobsCount()
    {
        if (!$this->isSetFlag(self::MULTI_PROCESS_GROUP, self::ENABLED_FIELD)) {
            return 1;
        }

        if (!function_exists('pcntl_fork')) {
            return 1;
        }

        $processCount = (int)$this->getScopeValue(self::MULTI_PROCESS_GROUP, self::PROCESS_COUNT_FIELD);

        return $processCount > 1 ? $processCount : 1;
    }
}

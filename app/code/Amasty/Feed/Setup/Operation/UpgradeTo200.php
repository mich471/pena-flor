<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Magento\Email\Model\Template;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo200 implements OperationInterface
{
    private const MODULE_DIR = 'Amasty_Feed';
    public const SUCCESS_TEMPLATE_NAME = 'amasty_feed_notifications_success_template';
    private const SUCCESS_TEMPLATE_SUBJECT = 'Amasty Successful Feed Generation';
    public const UNSUCCESS_TEMPLATE_NAME = 'amasty_feed_notifications_unsuccess_template';
    private const UNSUCCESS_TEMPLATE_SUBJECT  = 'Amasty Unsuccessful Feed Generation';
    private const TEMPLATE_VARIABLES = 'amasty_feed_notifications_generation_variables';

    /**
     * @var Template
     */
    private $template;

    /**
     * @var File
     */
    private $filesystem;

    /**
     * @var Reader
     */
    private $moduleReader;

    public function __construct(
        Template $template,
        File $filesystem,
        Reader $moduleReader
    ) {
        $this->template = $template;
        $this->filesystem = $filesystem;
        $this->moduleReader = $moduleReader;
    }

    /**
     * @throws FileSystemException
     */
    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.0.0', '<')) {
            $templateVars = $this->filesystem->fileGetContents(
                $this->getDirectory(self::TEMPLATE_VARIABLES . '.html')
            );
            $this->createGenerationEmailTemplate(
                $templateVars,
                self::SUCCESS_TEMPLATE_NAME,
                self::SUCCESS_TEMPLATE_SUBJECT
            );
            $this->createGenerationEmailTemplate(
                $templateVars,
                self::UNSUCCESS_TEMPLATE_NAME,
                self::UNSUCCESS_TEMPLATE_SUBJECT
            );
        }
    }

    /**
     * @param string $templateVars
     * @param string $templateName
     * @param string $templateSubject
     * @throws FileSystemException
     */
    private function createGenerationEmailTemplate($templateVars, $templateName, $templateSubject)
    {
        $templateText = $this->filesystem->fileGetContents($this->getDirectory($templateName . '.html'));
        $templateData = [
            'template_code' => $templateSubject,
            'template_subject' => $templateSubject,
            'template_type' => Template::TYPE_HTML,
            'template_text' => $templateText,
            'orig_template_variables' => $templateVars,
            'orig_template_code' => $templateName
        ];
        $this->template->setData($templateData)
            ->save();
    }

    /**
     * @param string $templateName
     * @return string
     */
    private function getDirectory($templateName)
    {
        $viewDir = $this->moduleReader->getModuleDir(
            Dir::MODULE_VIEW_DIR,
            self::MODULE_DIR
        );

        return $viewDir . '/frontend/email/' . $templateName;
    }
}

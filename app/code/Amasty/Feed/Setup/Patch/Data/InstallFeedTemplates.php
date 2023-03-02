<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Patch\Data;

use Amasty\Feed\Setup\SampleData\Installer;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Setup;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallFeedTemplates implements DataPatchInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
    private $executor;

    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ModuleList
     */
    private $moduleList;

    public function __construct(
        Setup\SampleData\Executor $executor,
        Installer $installer,
        ModuleList $moduleList
    ) {
        $this->executor = $executor;
        $this->installer = $installer;
        $this->moduleList = $moduleList;
    }

    public function apply()
    {
        $moduleConfig = $this->moduleList->getOne('Amasty_Feed');
        $setupVersion = $moduleConfig['setup_version'] ?? false;

        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install fixtures, because setup_version is a marker.
        if (!$setupVersion) {
            $this->executor->exec($this->installer);
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}

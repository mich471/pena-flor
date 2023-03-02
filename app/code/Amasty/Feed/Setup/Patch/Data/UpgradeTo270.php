<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Patch\Data;

use Amasty\Feed\Setup\Operation\OperationInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchHistory;

class UpgradeTo270 implements DataPatchInterface
{
    private const OLD_PATCH_NAME = 'Amasty\Feed\Setup\Patch\Data\UpgradeDataTo270';

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var PatchHistory
     */
    private $patchHistory;

    /**
     * @var array
     */
    private $operations;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $moduleDataSetup,
        State $appState,
        PatchHistory $patchHistory,
        array $operations = []
    ) {
        $this->moduleResource = $moduleResource;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->appState = $appState;
        $this->patchHistory = $patchHistory;
        $this->operations = $operations;
    }

    public function apply()
    {
        // we renamed the patch because it didn't pass MM validation.
        // this fix prevents the patch from running if it was applied by the old name.
        if (!$this->patchHistory->isApplied(self::OLD_PATCH_NAME)) {
            $this->appState->emulateAreaCode(
                Area::AREA_ADMINHTML,
                [$this, 'upgradeDataWithEmulationAreaCode']
            );
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

    public function upgradeDataWithEmulationAreaCode()
    {
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_Feed');
        $this->moduleDataSetup->startSetup();
        foreach ($this->operations as $operation) {
            if ($operation instanceof OperationInterface) {
                $operation->execute($this->moduleDataSetup, $setupDataVersion);
            }
        }
        $this->moduleDataSetup->endSetup();
    }
}

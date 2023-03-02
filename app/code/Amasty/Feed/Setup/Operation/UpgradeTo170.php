<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Model\Indexer\Feed\IndexBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\FlagManager;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo170 implements OperationInterface
{
    /**
     * @var IndexBuilder
     */
    private $feedBuilder;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var FlagManager
     */
    private $flagManager;

    public function __construct(
        IndexBuilder $feedBuilder,
        State $appState,
        FlagManager $flagManager
    ) {
        $this->feedBuilder = $feedBuilder;
        $this->appState = $appState;
        $this->flagManager = $flagManager;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '1.7.0', '<')
            && !$this->flagManager->getFlagData('amasty_feed_upg_to_170')
        ) {
            $this->flagManager->saveFlag('amasty_feed_upg_to_170', true);
            $this->appState->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$this, 'reindexFeed']
            );
        }
    }

    public function reindexFeed()
    {
        $this->feedBuilder->reindexFull();
    }
}

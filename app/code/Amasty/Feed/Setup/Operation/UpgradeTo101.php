<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Setup\SampleData\Updater;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Executor;

class UpgradeTo101 implements OperationInterface
{
    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var Updater
     */
    private $updater;

    public function __construct(
        Executor $executor,
        Updater $updater
    ) {
        $this->executor = $executor;
        $this->updater = $updater;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '1.0.1') < 0) {
            $this->updater->setTemplates(['bing']);
            $this->executor->exec($this->updater);
        }
    }
}

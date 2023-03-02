<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;

interface OperationInterface
{
    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void;
}

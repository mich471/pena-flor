<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Model\Import;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo236 implements OperationInterface
{
    /**
     * @var Import
     */
    private $import;

    public function __construct(
        Import $import
    ) {
        $this->import = $import;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.3.6', '<')) {
            $this->import->update(['amazonProduct', 'amazonInventory', 'amazonPrice', 'amazonImage']);
        }
    }
}

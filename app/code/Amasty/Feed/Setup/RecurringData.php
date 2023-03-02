<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup;

use Amasty\Feed\Setup\Operation\OperationInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class RecurringData implements InstallDataInterface
{
    /**
     * @var array
     */
    private $operations;

    public function __construct(
        array $operations = []
    ) {
        $this->operations = $operations;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->upgradeTo270($setup, $context);
    }

    private function upgradeTo270(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        foreach ($this->operations as $operation) {
            if ($operation instanceof OperationInterface) {
                $operation->execute($setup, (string)$context->getVersion());
            }
        }
        $setup->endSetup();
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo114 implements OperationInterface
{
    /**
     * @var Repository
     */
    private $attributeRepository;

    public function __construct(Repository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.3.1', '<')) {
            $attributesForConditions = ['status', 'quantity_and_stock_status'];
            foreach ($attributesForConditions as $code) {
                $attribute = $this->attributeRepository->get($code);
                $attribute->setIsUsedForPromoRules(true);
                $this->attributeRepository->save($attribute);
            }
        }
    }
}

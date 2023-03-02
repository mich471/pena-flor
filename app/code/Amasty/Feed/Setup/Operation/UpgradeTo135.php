<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Base\Setup\SerializedFieldDataConverter;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo135 implements OperationInterface
{
    /**
     * @var SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetaData;

    public function __construct(
        SerializedFieldDataConverter $fieldDataConverter,
        ProductMetadataInterface $productMetaData
    ) {
        $this->fieldDataConverter = $fieldDataConverter;
        $this->productMetaData = $productMetaData;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '1.3.5', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $this->fieldDataConverter->convertSerializedDataToJson(
                'amasty_feed_entity',
                'entity_id',
                ['conditions_serialized']
            );
        }
    }
}

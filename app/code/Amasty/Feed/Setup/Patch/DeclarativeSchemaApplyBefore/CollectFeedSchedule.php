<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\Feed\Setup\Operation\MigrateFeedSchedule\CollectFeedScheduleData;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\Feed\Model\ResourceModel\Feed;

class CollectFeedSchedule implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CollectFeedScheduleData
     */
    private $collectFeedScheduleData;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectFeedScheduleData $collectFeedScheduleData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->collectFeedScheduleData = $collectFeedScheduleData;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $feedTable = $this->moduleDataSetup->getTable(Feed::TABLE_NAME);
        if ($this->moduleDataSetup->getConnection()->isTableExists($feedTable)
            && $this->moduleDataSetup->getConnection()->tableColumnExists($feedTable, 'cron_day')
            && $this->moduleDataSetup->getConnection()->tableColumnExists($feedTable, 'cron_time')
        ) {
            $this->collectFeedScheduleData->execute();
        }
    }
}

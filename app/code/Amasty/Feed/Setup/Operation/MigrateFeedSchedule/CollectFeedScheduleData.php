<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation\MigrateFeedSchedule;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Api\Data\ScheduleInterface;
use Amasty\Feed\Model\CronProvider;
use Amasty\Feed\Model\ResourceModel\Feed\Collection;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;

class CollectFeedScheduleData
{
    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var ScheduleRegistry
     */
    private $scheduleRegistry;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        ScheduleRegistry $scheduleRegistry
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->scheduleRegistry = $scheduleRegistry;
    }

    public function execute()
    {
        /** @var Collection $feedCollection */
        $feedCollection = $this->feedCollectionFactory->create();
        $feedCollection->addFieldToFilter('is_template', 0);

        $this->collectScheduleData($feedCollection->getData());
    }

    private function collectScheduleData(array $feeds)
    {
        $scheduleData = [];

        foreach ($feeds as $feed) {
            if ($feed['cron_day'] != CronProvider::EVERY_DAY) {
                $scheduleData[] = $this->createScheduleData($feed, $feed['cron_day']);
            } else {
                for ($i = 0; $i < CronProvider::EVERY_DAY; $i++) {
                    $scheduleData[] = $this->createScheduleData($feed, $i);
                }
            }
        }

        $this->scheduleRegistry->register(ScheduleRegistry::SCHEDULE_DATA, $scheduleData);
    }

    private function createScheduleData(array $feed, $cronDay): array
    {
        return [
            ScheduleInterface::CRON_DAY => $cronDay,
            ScheduleInterface::CRON_TIME => $feed['cron_time'],
            ScheduleInterface::FEED_ID => $feed[FeedInterface::ENTITY_ID]
        ];
    }
}

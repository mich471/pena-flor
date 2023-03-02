<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Api\Data\ValidProductsInterface;
use Amasty\Feed\Model\Config\Source\ExecuteModeList;
use Amasty\Feed\Model\Config\Source\FeedStatus;
use Amasty\Feed\Model\Feed as FeedModel;
use Amasty\Feed\Model\Import;
use Amasty\Feed\Model\ResourceModel\Feed\Collection;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Amasty\Feed\Model\Schedule\ResourceModel\Schedule as ScheduleResource;
use Amasty\Feed\Model\ValidProduct\ResourceModel\CollectionFactory as ValidProductsCollectionFactory;
use Amasty\Feed\Setup\Operation\MigrateFeedSchedule\ScheduleRegistry;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo220 implements OperationInterface
{
    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var ValidProductsCollectionFactory
     */
    private $validProductsFactory;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var ScheduleRegistry
     */
    private $scheduleRegistry;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        ValidProductsCollectionFactory $validProductsFactory,
        Import $import,
        ScheduleRegistry $scheduleRegistry
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->validProductsFactory = $validProductsFactory;
        $this->import = $import;
        $this->scheduleRegistry = $scheduleRegistry;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.2.0', '<')) {
            $this->import->update('google');

            /** @var Collection $feedCollection */
            $feedCollection = $this->feedCollectionFactory->create();
            $feedCollection->addFieldToFilter('is_template', 0);

            /** @var FeedModel $feed */
            foreach ($feedCollection->getItems() as $feed) {
                $this->fillDataToNewColumns($feed);
                $this->addOptionModificatorToExisted($feed);
            }

            $feedCollection->save();

            $this->transferScheduleData($moduleDataSetup);
        }
    }

    private function transferScheduleData(ModuleDataSetupInterface $moduleDataSetup)
    {
        $scheduleData = (array)$this->scheduleRegistry->registry(ScheduleRegistry::SCHEDULE_DATA);
        if ($scheduleData) {
            $moduleDataSetup->getConnection()->insertMultiple(
                $moduleDataSetup->getTable(ScheduleResource::TABLE_NAME),
                $scheduleData
            );
        }
    }

    /**
     * @param FeedModel $feed
     */
    private function addOptionModificatorToExisted($feed)
    {
        if ($feed->isXml()) {
            $content = $feed->getXmlContent();
            $content = str_replace('modify=', 'optional="no" modify=', $content);
            $feed->setXmlContent($content);
        }
    }

    /**
     * @param FeedModel $feed
     */
    private function fillDataToNewColumns($feed)
    {
        $validProductsCollection = $this->validProductsFactory->create();
        $validProductsCollection->addFieldToFilter(ValidProductsInterface::FEED_ID, $feed->getId());
        $feed->setProductsAmount($validProductsCollection->getSize());
        $feed->setStatus($feed->getGeneratedAt() ? FeedStatus::READY : FeedStatus::NOT_GENERATED);
        $feed->setGenerationType(
            $feed->getExecuteMode() === 'manual'
                ? ExecuteModeList::MANUAL_GENERATED
                : ExecuteModeList::CRON_GENERATED
        );
    }
}

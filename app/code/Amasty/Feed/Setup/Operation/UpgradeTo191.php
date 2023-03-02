<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Model\CronProvider;
use Amasty\Feed\Model\Feed as FeedModel;
use Amasty\Feed\Model\Import;
use Amasty\Feed\Model\ResourceModel\Feed as FeedResource;
use Amasty\Feed\Model\ResourceModel\Feed\Collection;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo191 implements OperationInterface
{
    private const NOT_SUPPORTED = ['hourly', 'daily', 'weekly', 'monthly'];

    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var FeedResource
     */
    private $resourceModelFeed;

    /**
     * @var Import
     */
    private $import;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        FeedResource $resourceModelFeed,
        Import $import
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->resourceModelFeed = $resourceModelFeed;
        $this->import = $import;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '1.9.1', '<')) {
            $this->import->update('google');

            /** @var Collection $feedCollection */
            $feedCollection = $this->feedCollectionFactory->create();
            $feeds = $feedCollection->addFieldToFilter(
                'execute_mode',
                ['in' => self::NOT_SUPPORTED]
            )->getItems();

            /** @var FeedModel $feed */
            foreach ($feeds as $feed) {
                switch ($feed->getExecuteMode()) {
                    case 'hourly':
                    case 'daily':
                        $feed->setCronDay(CronProvider::EVERY_DAY);
                        $feed->setCronTime(0);
                        break;

                    case 'weekly':
                    case 'monthly':
                        $feed->setCronDay('1');
                        $feed->setCronTime(0);
                        break;
                }

                $feed->setExecuteMode('schedule');
                $this->resourceModelFeed->save($feed);
            }
        }
    }
}

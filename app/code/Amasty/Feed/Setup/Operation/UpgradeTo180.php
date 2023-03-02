<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Model\Feed as FeedModel;
use Amasty\Feed\Model\ResourceModel\Feed as FeedResource;
use Amasty\Feed\Model\ResourceModel\Feed\Collection;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo180 implements OperationInterface
{
    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var FeedResource
     */
    private $resourceModelFeed;

    public function __construct(
        CollectionFactory $feedCollectionFactory,
        FeedResource $resourceModelFeed
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->resourceModelFeed = $resourceModelFeed;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '1.8.0', '<')) {
            /** @var Collection $feedCollection */
            $feedCollection = $this->feedCollectionFactory->create();
            $feedCollection->addFieldToFilter('is_template', '1')
                ->addFieldToFilter('name', 'Google')
                ->addFieldToFilter('feed_type', 'xml');

            /** @var FeedModel $feed */
            $feed = $feedCollection->getFirstItem();

            if ($feed) {
                $feed->setXmlHeader($feed->getXmlHeader() . '<created_at>{{DATE}}</created_at>');
                $this->resourceModelFeed->save($feed);
            }
        }
    }
}

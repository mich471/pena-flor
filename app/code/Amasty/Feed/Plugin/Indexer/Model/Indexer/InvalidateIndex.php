<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Plugin\Indexer\Model\Indexer;

use Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor;
use Amasty\Feed\Model\Indexer\LockManager;
use Amasty\Feed\Model\Indexer\Product\ProductFeedProcessor;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Indexer\StateInterface;
use Magento\Indexer\Model\Indexer;

class InvalidateIndex
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var LockManager
     */
    private $locker;

    public function __construct(
        IndexerRegistry $indexerRegistry,
        LockManager $locker
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->locker = $locker;
    }

    public function aroundReindexAll(Indexer $subject, callable $proceed): ?callable
    {
        if ($this->locker->isProcessLocked()
            && in_array($subject->getId(), [FeedRuleProcessor::INDEXER_ID, ProductFeedProcessor::INDEXER_ID])) {
            $indexer = $this->indexerRegistry->get($subject->getId());
            if ($indexer->getStatus() == StateInterface::STATUS_VALID) {
                $indexer->invalidate();
            }

            return null;
        }

        return $proceed();
    }
}

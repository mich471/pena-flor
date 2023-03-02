<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Indexer\Product;

use Amasty\Feed\Model\Indexer\AbstractIndexer;
use Amasty\Feed\Model\Indexer\LockManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class ProductFeedIndexer extends AbstractIndexer
{
    /**
     * @var LockManager
     */
    private $lockManager;

    /**
     * Override constructor. Indexer is changed
     *
     * @param IndexBuilder $productIndexBuilder
     * @param ManagerInterface $eventManager
     * @param LockManager $lockManager
     */
    //phpcs:ignore
    public function __construct(
        IndexBuilder $productIndexBuilder,
        ManagerInterface $eventManager,
        LockManager $lockManager
    ) {
        parent::__construct($productIndexBuilder, $eventManager);
        $this->lockManager = $lockManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecuteList($productIds)
    {
        $this->indexBuilder->reindexByProductIds(array_unique($productIds));
        $this->getCacheContext()->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $productIds);
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecuteRow($productId)
    {
        $this->indexBuilder->reindexByProductId($productId);
    }

    public function executeFull()
    {
        $this->lockManager->lockProcess();
        try {
            $this->indexBuilder->reindexFull();
        } catch (\Exception $e) {
            $this->lockManager->unlockProcess();
            throw new LocalizedException(__($e->getMessage()), $e);
        }
        $this->lockManager->unlockProcess();
    }
}

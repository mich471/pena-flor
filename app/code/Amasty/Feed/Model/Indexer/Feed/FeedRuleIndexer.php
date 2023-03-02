<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Indexer\Feed;

use Amasty\Feed\Model\Indexer\AbstractIndexer;
use Amasty\Feed\Model\Indexer\LockManager;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class FeedRuleIndexer extends AbstractIndexer
{
    /**
     * @var LockManager
     */
    private $lockManager;

    /**
     * Override constructor. Indexer is changed
     *
     * @param IndexBuilder $indexBuilder
     * @param ManagerInterface $eventManager
     * @param LockManager $lockManager
     */
    public function __construct(
        IndexBuilder $indexBuilder,
        ManagerInterface $eventManager,
        LockManager $lockManager
    ) {
        parent::__construct($indexBuilder, $eventManager);
        $this->indexBuilder = $indexBuilder;
        $this->lockManager = $lockManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecuteList($ids)
    {
        $this->indexBuilder->reindexByFeedIds(array_unique($ids));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecuteRow($id)
    {
        $this->indexBuilder->reindexByFeedId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [
            Block::CACHE_TAG
        ];
    }

    /**
     * {@inheritdoc}
     */
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

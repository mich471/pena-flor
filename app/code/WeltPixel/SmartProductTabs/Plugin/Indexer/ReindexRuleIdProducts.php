<?php

namespace WeltPixel\SmartProductTabs\Plugin\Indexer;

use WeltPixel\SmartProductTabs\Model\SmartProductTabs;
use Magento\Framework\Indexer\IndexerRegistry;
use WeltPixel\SmartProductTabs\Model\Indexer\Rule\RuleProductIndexer;

class ReindexRuleIdProducts
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var RuleProductIndexer
     */
    protected $smartProductTabRuleIndexer;

    /**
     * ReindexRuleIdProducts constructor.
     * @param IndexerRegistry $indexerRegistry
     * @param RuleProductIndexer $smartProductTabRuleIndexer
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        RuleProductIndexer $smartProductTabRuleIndexer
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->smartProductTabRuleIndexer = $smartProductTabRuleIndexer;
    }

    /**
     * @param SmartProductTabs $subject
     * @param SmartProductTabs $result
     * @return SmartProductTabs
     */
    public function afterAfterSave(SmartProductTabs $subject, SmartProductTabs $result)
    {
        $ruleId = $subject->getId();
        $indexer = $this->indexerRegistry->get('weltpixel_smartproducttabs_rule');
        if ($indexer->isScheduled() == false) {
            $this->smartProductTabRuleIndexer->executeRow($ruleId);
        }
        return $result;
    }
}

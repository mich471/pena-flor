<?php

namespace WeltPixel\CmsBlockScheduler\Cron;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CleanExpiredBlocks
 * @package WeltPixel\CmsBlockScheduler\Cron
 */
class CleanExpiredBlocks
{
    /**
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @param BlockFactory $blockFactory
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        DateTime $date,
        ManagerInterface $eventManager
    ) {
        $this->_blockFactory = $blockFactory;
        $this->_date = $date;
        $this->_eventManager = $eventManager;
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @return void
     */
    public function execute()
    {
        $blockCollection = $this->_blockFactory->create()->getCollection();
        $blockCollection->addFieldToFilter('valid_from', ['notnull' => true]);
        $blockCollection->addFieldToFilter('valid_to', ['notnull' => true]);

        $now = $this->_date->gmtDate();

        foreach ($blockCollection->getItems() as $item) {
            $validFrom = $item->getValidFrom();
            $validTo   = $item->getValidTo();
            $isValid = 1;

            if (($validFrom && $validFrom > 0) && ($validFrom > $now)) {
                $isValid  = 0;
            } elseif (($validTo && $validTo > 0) && ($validTo < $now)) {
                $isValid = 0;
            }

            if ($item->getData('cron_schedule_flag') != $isValid) {
                $item->setData('ignore_cron_schedule_flag', true);
                $item->setData('cron_schedule_flag', $isValid);
                try {
                    $item->save();
                    $this->_eventManager->dispatch('clean_cache_by_tags', ['object' => $item]);
                } catch (\Exception $ex) {
                }
            }
        }
    }
}

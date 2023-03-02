<?php

namespace WeltPixel\ProductPage\Cron;

use WeltPixel\ProductPage\Model\VisitorCounterManager;

/**
 * Class VisitorCounter
 * @package WeltPixel\ProductPage\Cron
 */
class VisitorCounter
{
    /**
     * @var VisitorCounterManager
     */
    protected $visitorCounterManager;

    /**
     * VisitorCounter constructor.
     * @param VisitorCounterManager $visitorCounterManager
     */
    public function __construct(
        VisitorCounterManager $visitorCounterManager
    ) {
        $this->visitorCounterManager = $visitorCounterManager;
    }

    /**
     * @return \WeltPixel\Backend\Cron\VisitorCounter
     */
    public function execute()
    {
        $this->visitorCounterManager->clearLogTable();
    }
}

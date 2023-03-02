<?php

namespace BalloonGroup\NewLogin\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class Register implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->coreRegistry = $registry;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->coreRegistry->register('is_new_account', true);
    }
}

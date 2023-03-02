<?php

namespace Lyracons\Icommkt\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Lyracons\Icommkt\Helper\ContactSync;
use Lyracons\Icommkt\Logger\Logger;

class SubscribeEmail implements ObserverInterface
{

    /**
     * @var ContactSync $contactSync
     */
    protected $contactSync;

    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * SubscribeEmail constructor.
     * @param ContactSync $contactSync
     */
    public function __construct(
        ContactSync $contactSync,
        Logger $logger
    )
    {
        $this->contactSync = $contactSync;
        $this->logger = $logger;
        $this->logger->setEnabled($contactSync->isLogEnabled())
            ->setVerboseMode($contactSync->isLogVerbose());
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $email = $observer->getEvent()->getData('email');
        if (is_null($email)) {
            return $this;
        }
        $this->logger->debug('subscribe email');
        $this->logger->verbose('subscribe email details: ' . "\n" . var_export($email, true));
        $this->contactSync->send($email);

        return $this;
    }

}

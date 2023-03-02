<?php

namespace BalloonGroup\NewsletterIcommktIntegration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ManagerInterface;
use Lyracons\Icommkt\Helper\ContactSync;
use Lyracons\Icommkt\Logger\Logger;

class NewsletterSubscription implements ObserverInterface
{

    /**
     * @var ContactSync $contactSync
     */
    protected $contactSync;

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var Logger $logger
     */
    protected $logger;

    public function __construct(
        ContactSync $contactSync,
        ManagerInterface $eventManager,
        Logger $logger
    ) {
        $this->_eventManager = $eventManager;
        $this->contactSync = $contactSync;
        $this->logger = $logger;
        $this->logger->setEnabled($contactSync->isLogEnabled())
            ->setVerboseMode($contactSync->isLogVerbose());
    }

    /**
     * @todo integration with Icommkt shouldn't be handled here
     * 
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $email = $subscriber->getEmail();

        if (is_null($email)) {
            return $this;
        }

        $this->logger->debug('subscribe email');
        $this->logger->verbose('subscribe email details: ' . "\n" . var_export($email, true));
        $this->contactSync->send($email);

        return $this;
        /* $this->_eventManager->dispatch(
            'lyracons_icommkt_external_email_suscribe',
            ['email', $subscriberEmail]
        ); */
    }
}
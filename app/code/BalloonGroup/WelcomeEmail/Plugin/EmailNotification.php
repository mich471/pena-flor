<?php

namespace BalloonGroup\WelcomeEmail\Plugin;
class EmailNotification
{
    public function aroundNewAccount(\Magento\Customer\Model\EmailNotification $subject, callable $proceed)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get(\BalloonGroup\General\Logger\Logger::class);
        $logger->info('NOTIFICACIÓN BIENVENIDA EMAIL DISABLED');
        return $subject;
    }
}
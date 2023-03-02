<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Observer;

use Amasty\Feed\Api\Data\FeedInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class ExportFtpUpload implements ObserverInterface
{
    /**
     * @var \Amasty\Feed\Model\Filesystem\Ftp
     */
    private $ftp;

    public function __construct(\Amasty\Feed\Model\Filesystem\Ftp $ftp)
    {
        $this->ftp = $ftp;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var FeedInterface $feed */
        if ($feed = $observer->getData('feed')) {
            if ($feed->getDeliveryEnabled()) {
                switch ($feed->getDeliveryType()) {
                    case 'ftp':
                        $this->ftp->ftpUpload($feed);
                        break;
                    case 'sftp':
                        $this->ftp->sftpUpload($feed);
                        break;
                    default:
                        throw new LocalizedException(__('Invalid protocol'));
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Amasty\Feed\Controller\Adminhtml\AbstractFeed;
use Amasty\Feed\Model\Indexer\LockManager;
use Magento\Backend\App\Action\Context;

class Unlock extends AbstractFeed
{
    /**
     * @var LockManager
     */
    private $lockManager;

    public function __construct(
        Context $context,
        LockManager $lockManager
    ) {
        parent::__construct($context);
        $this->lockManager = $lockManager;
    }

    public function execute()
    {
        $this->lockManager->unlockProcess();
        $this->messageManager->addSuccessMessage(__('Unlocked successfully!'));

        return $this->resultRedirectFactory->create()->setPath('amfeed/*');
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Category;

use Amasty\Feed\Controller\Adminhtml\AbstractCategory;
use Amasty\Feed\Model\Category\Repository;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;

class Delete extends AbstractCategory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Repository $repository,
        LoggerInterface $logger,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function execute()
    {
        if ($categoryId = $this->getRequest()->getParam('feed_category_id')) {
            try {
                $this->repository->deleteById($categoryId);
                $this->messageManager->addSuccessMessage(__('You deleted the category mapping.'));

                return $this->resultRedirectFactory->create()->setPath('amfeed/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the category mapping right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $this->resultRedirectFactory->create()->setPath(
                    'amfeed/*/edit',
                    ['id' => $this->getRequest()->getParam('id')]
                );
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a category mapping to delete.'));

        return $this->resultRedirectFactory->create()->setPath('amfeed/*/');
    }
}

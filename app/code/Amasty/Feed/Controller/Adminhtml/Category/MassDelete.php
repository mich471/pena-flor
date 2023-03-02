<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Category;

use Amasty\Feed\Controller\Adminhtml\AbstractCategory;
use Amasty\Feed\Model\Category\Repository;
use Amasty\Feed\Model\Category\ResourceModel\Collection;
use Amasty\Feed\Model\Category\ResourceModel\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends AbstractCategory
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Repository $repository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function execute()
    {
        try {
            $this->filter->applySelectionOnTargetProvider();
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->isCollectionExists($collection);
            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong. Please review the error log.')
            );
            $this->logger->critical($e);
        }

        return $this->resultRedirectFactory->create()->setPath('amfeed/*/index');
    }

    /**
     * @param $collection
     * @throws LocalizedException
     */
    private function isCollectionExists($collection)
    {
        if (!$collection->getSize()) {
            throw new LocalizedException(__('This category no longer exists.'));
        }
    }

    /**
     * @param AbstractDb $collection
     */
    public function massAction($collection)
    {
        $count = $collection->getSize();
        foreach ($collection->getAllIds() as $categoryId) {
            $this->repository->deleteById($categoryId);
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));
    }
}

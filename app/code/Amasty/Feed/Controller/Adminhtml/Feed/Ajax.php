<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Api\Data\ValidProductsInterface;
use Amasty\Feed\Api\FeedRepositoryInterface;
use Amasty\Feed\Controller\Adminhtml\AbstractFeed;
use Amasty\Feed\Exceptions\LockProcessException;
use Amasty\Feed\Model\Config;
use Amasty\Feed\Model\Config\Source\ExecuteModeList;
use Amasty\Feed\Model\Config\Source\FeedStatus;
use Amasty\Feed\Model\FeedExport;
use Amasty\Feed\Model\Filesystem\FeedOutput;
use Amasty\Feed\Model\Indexer\LockManager;
use Amasty\Feed\Model\ValidProduct\ResourceModel\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class Ajax extends AbstractFeed
{
    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FeedExport
     */
    private $feedExport;

    /**
     * @var FeedOutput
     */
    private $feedOutput;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LockManager
     */
    private $lockManager;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        UrlFactory $urlFactory,
        CollectionFactory $collectionFactory,
        FeedRepositoryInterface $feedRepository,
        Config $config,
        FeedExport $feedExport,
        FeedOutput $feedOutput,
        LockManager $lockManager
    ) {
        $this->urlFactory = $urlFactory;
        $this->feedRepository = $feedRepository;
        $this->config = $config;

        parent::__construct($context);
        $this->logger = $logger;
        $this->feedExport = $feedExport;
        $this->feedOutput = $feedOutput;
        $this->collectionFactory = $collectionFactory;
        $this->lockManager = $lockManager;
    }

    /**
     * @return UrlInterface
     */
    private function getUrlInstance()
    {
        return $this->urlFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $page = (int)$this->getRequest()->getParam('page', 0);
        $feedId = $this->getRequest()->getParam('feed_entity_id');
        $body = [];
        $feed = null;
        $currentPage = $page + 1; // Valid page for searchCriteria

        try {
            if ($currentPage == 1) {
                $this->lockManager->lockProcess();
            }
            $itemsPerPage = (int)$this->config->getItemsPerPage();
            $lastPage = false;
            /** @var FeedInterface $feed */
            $feed = $this->feedRepository->getById($feedId);

            $feed->setGenerationType(ExecuteModeList::MANUAL_GENERATED);

            if ($page === 0) {
                $feed->setProductsAmount(0);
            }

            $validProductsCollection = $this->collectionFactory->create();
            $validProductsCollection->addFieldToFilter(ValidProductsInterface::FEED_ID, $feedId)
                ->setPageSize($itemsPerPage)
                ->setCurPage($currentPage)
                ->addFieldToSelect(ValidProductsInterface::VALID_PRODUCT_ID);
            $collectionSize = $validProductsCollection->getSize();
            $validProducts = array_map(function ($item) {
                return $item[ValidProductsInterface::VALID_PRODUCT_ID];
            }, $validProductsCollection->getData());

            $totalPages = ceil($collectionSize / $itemsPerPage);

            if ((int)$page == $totalPages - 1 || $totalPages == 0) {
                $lastPage = true;
            }

            if (count($validProducts) === 0) {
                throw new NotFoundException(__('There are no products to generate feed. Please check Amasty Feed'
                    . ' indexers status or feed conditions.'));
            }

            $this->feedExport->export($feed, $page, $validProducts, $lastPage);

            $body['exported'] = count($validProducts);
            $body['isLastPage'] = $lastPage;
            $body['total'] = $collectionSize;
        } catch (LockProcessException $e) {
            $body['error'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);

            $feed->setStatus(FeedStatus::FAILED);
            $this->feedRepository->save($feed);

            $body['error'] = $e->getMessage();
            $this->lockManager->unlockProcess();
        }

        if (!isset($body['error'])) {
            $urlInstance = $this->getUrlInstance();

            $routeParams = [
                '_direct' => 'amfeed/feed/download',
                '_query' => [
                    'id' => $feed->getEntityId()
                ]
            ];

            $href = $urlInstance
                ->setScope($feed->getStoreId())
                ->getUrl(
                    '',
                    $routeParams
                );

            if (!empty($body['isLastPage'])) {
                $feedOutput = $this->feedOutput->get($feed);
                $body['download'] = $href . "&file=" . $feedOutput['filename'];
            }
        } else {
            $body['error'] = substr($body['error'], 0, 150) . '...';
        }
        if (isset($lastPage)
            && $lastPage === true
            && empty($body['error'])
        ) {
            $this->lockManager->unlockProcess();
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($body);

        return $resultJson;
    }
}

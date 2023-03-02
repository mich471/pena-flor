<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Cron;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Api\Data\ValidProductsInterface;
use Amasty\Feed\Api\FeedRepositoryInterface;
use Amasty\Feed\Exceptions\LockProcessException;
use Amasty\Feed\Model\Config;
use Amasty\Feed\Model\Config\Source\Events;
use Amasty\Feed\Model\Config\Source\ExecuteModeList;
use Amasty\Feed\Model\Config\Source\FeedStatus;
use Amasty\Feed\Model\CronProvider;
use Amasty\Feed\Model\EmailManagement;
use Amasty\Feed\Model\Feed;
use Amasty\Feed\Model\FeedExport;
use Amasty\Feed\Model\FeedExportFactory;
use Amasty\Feed\Model\Indexer\LockManager;
use Amasty\Feed\Model\JobManager;
use Amasty\Feed\Model\JobManagerFactory as JobManagerFactory;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory as FeedCollectionFactory;
use Amasty\Feed\Model\Schedule\Management as ScheduleManagement;
use Amasty\Feed\Model\Schedule\ResourceModel\CollectionFactory as ScheduleCollectionFactory;
use Amasty\Feed\Model\ValidProduct\ResourceModel\Collection as ValidProductsCollection;
use Amasty\Feed\Model\ValidProduct\ResourceModel\CollectionFactory as ValidProductsFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

class RefreshData
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var FeedCollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ValidProductsFactory
     */
    private $validProductsFactory;

    /**
     * @var EmailManagement
     */
    private $emailManagement;

    /**
     * @var ScheduleManagement
     */
    private $scheduleManagement;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var JobManagerFactory
     */
    private $jobManagerFactory;

    /**
     * @var FeedExportFactory
     */
    private $feedExportFactory;

    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    /**
     * @var LockManager
     */
    private $lockManager;

    public function __construct(
        DateTime $dateTime,
        TimezoneInterface $localeDate,
        LoggerInterface $logger,
        FeedCollectionFactory $feedCollectionFactory,
        Config $config,
        EmailManagement $emailManagement,
        ValidProductsFactory $validProductsFactory,
        ScheduleManagement $scheduleManagement,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        JobManagerFactory $jobManagerFactory,
        FeedRepositoryInterface $feedRepository,
        FeedExportFactory $feedExportFactory,
        LockManager $lockManager
    ) {
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
        $this->logger = $logger;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->config = $config;
        $this->validProductsFactory = $validProductsFactory;
        $this->emailManagement = $emailManagement;
        $this->scheduleManagement = $scheduleManagement;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->feedExportFactory = $feedExportFactory;
        $this->jobManagerFactory = $jobManagerFactory;
        $this->feedRepository = $feedRepository;
        $this->lockManager = $lockManager;
    }

    public function execute()
    {
        $itemsPerPage = (int)$this->config->getItemsPerPage();
        /** @var \Amasty\Feed\Model\ResourceModel\Feed\Collection $collection */
        $collection = $this->feedCollectionFactory->create();
        $collection->addFieldToFilter(FeedInterface::IS_ACTIVE, 1)
            ->addFieldToFilter(FeedInterface::EXECUTE_MODE, ExecuteModeList::CRON);

        $events = $this->config->getSelectedEvents() ?? '';
        $emails = $this->config->getEmails();
        $events = explode(",", $events);

        try {
            $this->lockManager->lockProcess();
        } catch (LockProcessException $e) {
            try {
                foreach ($collection as $feed) {
                    if ($this->onSchedule($feed)) {
                        $feed->setStatus(FeedStatus::GENERATE_NEXT_CRON);
                    }
                }
                $collection->save();
            } catch (\Exception $e) {
                return;
            }

            return;
        }

        $maxJobs = $this->config->getMaxJobsCount();
        $multiProcessMode = $maxJobs > 1;

        if ($multiProcessMode) {
            /** @var JobManager $jobManager */
            $jobManager = $this->jobManagerFactory->create(['maxJobs' => $maxJobs]);
        }

        /** @var FeedExport $feedExport */
        $feedExport = $this->feedExportFactory->create([
            'multiProcessMode' => $multiProcessMode
        ]);

        /** @var Feed $feed */
        foreach ($collection as $feed) {
            try {
                if (!$this->onSchedule($feed)) {
                    continue;
                }

                $page = 1;
                $lastPage = false;
                $generationTime = date('Y-m-d H:i:s');

                /** @var ValidProductsCollection $vProductsCollection */
                $vProductsCollection = $this->validProductsFactory->create()
                    ->setPageSize($itemsPerPage)->setCurPage($page);
                $vProductsCollection->addFieldToFilter(ValidProductsInterface::FEED_ID, $feed->getId());

                $feed->setGenerationType(ExecuteModeList::CRON_GENERATED);
                $feed->setProductsAmount(0);

                while ($page <= $vProductsCollection->getLastPageNumber()) {
                    if ($page == $vProductsCollection->getLastPageNumber()) {
                        $lastPage = true;
                    }

                    $collectionData = $vProductsCollection->getData();
                    $productIds = [];

                    foreach ($collectionData as $datum) {
                        $productIds[] = $datum[ValidProductsInterface::VALID_PRODUCT_ID];
                    }

                    if ($productIds === []) {
                        throw new LocalizedException(__('There are no products to generate feed. Please check'
                            . ' Amasty Feed indexers status or feed conditions.'));
                    }

                    if ($multiProcessMode) {
                        $jobManager->waitForFreeSlot();

                        if (0 === $jobManager->fork()) { // Child process
                            $feedExport->export($feed, $page - 1, $productIds, $lastPage);

                            return;
                        }
                    } else {
                        $feedExport->export($feed, $page - 1, $productIds, $lastPage, false, $generationTime);
                    }

                    $vProductsCollection->setCurPage(++$page)->resetData();
                }

                if ($multiProcessMode) {
                    $jobManager->waitForAllJobs();
                    $feedExport->combineChunks($feed);
                    $feed->setProductsAmount($vProductsCollection->getSize());
                    $feed->setStatus(FeedStatus::READY);
                    $feed->setGeneratedAt($generationTime);
                    $this->feedRepository->save($feed);
                }

                if ($emails && $events && in_array(Events::SUCCESS, $events)) {
                    $emailTemplate = $this->config->getSuccessEmailTemplate();
                    $this->emailManagement->sendEmail($feed, $emailTemplate);
                }
            } catch (\Exception $e) {
                if ($emails && $events && in_array(Events::UNSUCCESS, $events)) {
                    $emailTemplate = $this->config->getUnsuccessEmailTemplate();
                    $this->emailManagement->sendEmail($feed, $emailTemplate, $e->getMessage());
                }

                $feed->setStatus(FeedStatus::FAILED);

                $this->logger->critical($e);
            }
        }

        $collection->save();
        $this->lockManager->unlockProcess();
    }

    /**
     * @param Feed $feed
     *
     * @return bool
     */
    private function validateTime($feed)
    {
        $mageTime = $this->localeDate->scopeTimeStamp();
        $now = (date("H", $mageTime) * 60) + date("i", $mageTime);

        /** @var \Amasty\Feed\Model\Schedule\ResourceModel\Collection $scheduleCollection */
        $scheduleCollection = $this->scheduleCollectionFactory->create();
        $scheduleCollection->addValidateTimeFilter($feed->getId(), $now, date('w'));

        return (bool)$scheduleCollection->getSize();
    }

    /**
     * @param Feed $feed
     *
     * @return bool
     */
    private function onSchedule($feed)
    {
        $currentDateTime = $this->dateTime->gmtDate();
        $lastValidDate = date(
            'Y-m-d H:i:s',
            strtotime($currentDateTime . '-' . CronProvider::MINUTES_IN_STEP . ' minutes')
        );

        return ($lastValidDate >= $feed->getGeneratedAt()
            && ($feed->getStatus() == FeedStatus::GENERATE_NEXT_CRON || $this->validateTime($feed)));
    }
}

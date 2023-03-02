<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Feed;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Model\Config\Source\FeedStatus;
use Amasty\Feed\Model\FeedRepository;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;

class Copier
{
    /**
     * @var FeedRepository
     */
    private $feedRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    public function __construct(
        FeedRepository $feedRepository,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory
    ) {
        $this->feedRepository = $feedRepository;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    private function duplicate(FeedInterface $feed): FeedInterface
    {
        $duplicate = $this->feedRepository->getEmptyModel();
        $duplicate->setData($feed->getData());
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalId($feed->getId());

        $duplicate->setExecuteMode('manual');
        $duplicate->setStatus(FeedStatus::NOT_GENERATED);
        $duplicate->setGeneratedAt(null);
        $duplicate->setId(null);
        $availableCurrencyCodes = $this->currencyFactory->create()->getConfigAllowCurrencies();

        if (!in_array($duplicate->getFormatPriceCurrency(), $availableCurrencyCodes)) {
            $duplicate->setFormatPriceCurrency($this->storeManager->getStore()->getDefaultCurrencyCode());
        }

        return $duplicate;
    }

    public function copy(FeedInterface $feed): FeedInterface
    {
        $duplicate = $this->duplicate($feed);

        $duplicate->setName($duplicate->getName() . '-duplicate');
        $duplicate->setFilename($duplicate->getFilename() . '-duplicate');

        return $this->feedRepository->save($duplicate, true);
    }

    /**
     * Create a new feed template based on this feed
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function template(FeedInterface $feed): FeedInterface
    {
        $duplicate = $this->duplicate($feed);

        $duplicate->setIsTemplate(true);
        $duplicate->setStoreId(null);

        return $this->feedRepository->save($duplicate, true);
    }

    public function fromTemplate(FeedInterface $template, $storeId): FeedInterface
    {
        $duplicate = $this->duplicate($template);

        $duplicate->setIsTemplate(false);
        $duplicate->setStoreId($storeId);

        return $this->feedRepository->save($duplicate, true);
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Api\FeedRepositoryInterface;
use Amasty\Feed\Model\Feed;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeTo227 implements OperationInterface
{
    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var FeedRepositoryInterface
     */
    private $feedRepository;

    public function __construct(
        FeedRepositoryInterface $feedRepository,
        CollectionFactory $feedCollectionFactory,
        EncryptorInterface $encryptor
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->encryptor = $encryptor;
        $this->feedRepository = $feedRepository;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.2.7', '<')) {
            $feeds = $this->feedCollectionFactory->create()->getItems();

            /** @var Feed $feed */
            foreach ($feeds as $feed) {
                $oldPass = $feed->getDeliveryPassword();

                if ($oldPass) {
                    $feed->setDeliveryPassword($this->encryptor->encrypt($feed->getDeliveryPassword()));
                    $this->feedRepository->save($feed);
                }
            }
        }
    }
}

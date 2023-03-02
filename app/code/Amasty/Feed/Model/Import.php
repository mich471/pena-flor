<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model;

use Amasty\Base\Model\Serializer as BaseSerializer;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class Import
{
    /**
     * @var Serialize
     */
    private $serializer;

    /**
     * @var string[]
     */
    private $templates = [
        'google', 'bing', 'shopping'
    ];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var BaseSerializer
     */
    private $baseSerializer;

    public function __construct(
        SampleDataContext $sampleDataContext,
        CollectionFactory $collectionFactory,
        FeedRepository $feedRepository,
        Filesystem $filesystem,
        Serialize $serializer,
        BaseSerializer $baseSerializer
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->collectionFactory = $collectionFactory;
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
        $this->feedRepository = $feedRepository;
        $this->baseSerializer = $baseSerializer;
    }

    public function install()
    {
        $dir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);

        foreach ($this->templates as $template) {
            $fileName = $dir->getRelativePath($this->fixtureManager->getFixture('Amasty_Feed::fixtures/' . $template));
            if (!$dir->isExist($fileName)) {
                continue;
            }

            try {
                $content = $dir->readFile($fileName);
            } catch (\Magento\Framework\Exception\FileSystemException $exception) {
                continue;
            }

            $data = $this->serializer->unserialize($content);

            if (is_array($data)) {
                if (isset($data['csv_field'])) {
                    $data['csv_field'] = $this->convertCsvFieldSerialization($data['csv_field']);
                }
                $feedCollection = $this->collectionFactory->create()
                    ->addFieldToFilter('name', $data['name'])
                    ->addFieldToFilter('is_template', 1);

                if ($feedCollection->getSize() > 0) {
                    $items = $feedCollection->getItems();
                    end($items)->delete();
                }

                $feedModel = $this->feedRepository->getEmptyModel();
                $feedModel->setData($data);
                try {
                    $this->feedRepository->save($feedModel);
                } catch (LocalizedException $exception) {
                    continue;
                }
            }
        }
    }

    public function update($templates)
    {
        if (!is_array($templates)) {
            $templates = [$templates];
        }

        $this->templates = $templates;

        $this->install();
    }

    private function convertCsvFieldSerialization($csvField)
    {
        try {
            $unserializedValue = $this->serializer->unserialize($csvField);
            $convertedValue = $this->baseSerializer->serialize($unserializedValue);

            return $convertedValue !== false ? $convertedValue : $csvField;
        } catch (\Exception $e) {
            return $csvField;
        }
    }
}

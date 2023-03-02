<?php

namespace WeltPixel\GoogleCards\Plugin;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\Page\DataProvider;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\GoogleCards\Model\Config\FileUploader\FileProcessor;

/**
 * Class PageDataProviderPlugin
 * @package WeltPixel\GoogleCards\Plugin
 */
class PageDataProviderPlugin
{
    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * PageRepositoryPlugin constructor.
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        FileProcessor $fileProcessor
    ) {
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @param DataProvider $subject
     * @param $loadedData
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetData(
        DataProvider $subject,
        $loadedData
    ) {
        /** @var array $loadedData */
        if (is_array($loadedData)) {
            foreach ($loadedData as $key => &$item) {
                if (isset($item['og_meta_image']) && strlen($item['og_meta_image'])) {
                    $imageArr = [];
                    $imageArr[0] = $this->fileProcessor->getImageDetails($item['og_meta_image']);
                    $item['og_meta_image'] = $imageArr;
                }
            }
        }

        return $loadedData;
    }
}

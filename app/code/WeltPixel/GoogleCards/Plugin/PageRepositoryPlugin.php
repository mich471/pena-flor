<?php

namespace WeltPixel\GoogleCards\Plugin;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\PageRepository;
use WeltPixel\GoogleCards\Model\Config\FileUploader\FileProcessor;

/**
 * Class PageRepositoryPlugin
 * @package WeltPixel\GoogleCards\Plugin
 */
class PageRepositoryPlugin
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
     * @param PageRepository $subject
     * @param PageInterface $page
     * @return PageInterface[]
     */
    public function beforeSave(
        PageRepository $subject,
        PageInterface $page
    ): array {
        $data = $page->getData();
        $imageField = 'og_meta_image';
        $entityImage = null;
        if (isset($data[$imageField])) {
            if (is_array($data[$imageField])) {
                $entityImage = $data[$imageField][0];
            } else {
                $entityImage = $data[$imageField];
            }
        }

        if ($entityImage && is_array($entityImage)) {
            /** Nothing was changed on the images */
            if (isset($entityImage['existingImage'])) {
                $data[$imageField] = $entityImage['existingImage'];
            } else {
                /** New Image was uploaded */
                try {
                    $entityImagePath = $this->fileProcessor->saveToPath($entityImage);
                    $data[$imageField] = $entityImagePath;
                } catch (\Exception $ex) {
                    $this->messageManager->addError($ex->getMessage());
                }
            }
        } else {
            $data[$imageField] = $entityImage;
        }

        $page->setData($data);
        return [$page];
    }
}

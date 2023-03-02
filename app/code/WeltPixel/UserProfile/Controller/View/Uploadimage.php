<?php

namespace WeltPixel\UserProfile\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\UserProfile\Model\UserProfile;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;

/**
 * Class Uploadimage
 * @package WeltPixel\UserProfile\Controller\View
 */
class Uploadimage extends Action
{
    /**
     * @type JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ImageAdapterFactory
     */
    protected $imageFactory;

    /**
     * Upload constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param UploaderFactory $fileUploaderFactory
     * @param Filesystem $fileSystem
     * @param StoreManagerInterface $storeManager
     * @param ImageAdapterFactory $imageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        UploaderFactory $fileUploaderFactory,
        Filesystem $fileSystem,
        StoreManagerInterface $storeManager,
        ImageAdapterFactory $imageFactory
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = [];
        $customerId = $this->getRequest()->getPostValue('customerId', null);
        $imageField = $this->getRequest()->getPostValue('image-field', null);

        $maxImageWidth = UserProfile::MEDIA_IMAGES_WIDTH;
        $imageDirectoryBasePath = UserProfile::MEDIA_IMAGES_PATH;
        if ($customerId) {
            $imageDirectoryBasePath .= DIRECTORY_SEPARATOR . $customerId;
        }
        try {
            $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $mediaUrl = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($imageDirectoryBasePath);
            $uploaderResult = $uploader->save($mediaUrl);
        } catch (\Exception $rx) {
            return $resultJson->setData([]);
        }
        $imagePath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        if ($uploaderResult['file']) {

            $currentPath = $this->fileSystem
                    ->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath($imageDirectoryBasePath) . $uploaderResult['file'];

            /** Resize the image if avatar was uploaded */
            if ($imageField == 'avatar') {
                $imageFactory = $this->imageFactory->create();
                $imageFactory->open($currentPath);
                $imageFactory->constrainOnly(true);
                $imageFactory->keepTransparency(true);
                $imageFactory->keepFrame(true);
                $imageFactory->keepAspectRatio(true);
                $imageFactory->resize(UserProfile::MEDIA_AVATAR_WIDTH, UserProfile::MEDIA_AVATAR_HEIGHT);
                $imageFactory->save($currentPath);
            }

            $imageFullpath = $imagePath . $imageDirectoryBasePath . $uploaderResult['file'];
            $size = getimagesize($currentPath);
            $ratio = $size[0] / $size[1];
            if ($size[0] > $maxImageWidth) {
                $size[0] = $maxImageWidth;
                $size[1] = $size[0] / $ratio;
            }
            $result['url'] = $imageFullpath;
            $result['size'] = $uploaderResult['size'];
            $result['width'] = $size[0];
            $result['height'] = $size[1];
        }
        return $resultJson->setData($result);
    }
}

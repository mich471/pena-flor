<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultInterface;
use Magento\ImportExport\Model\Import\SampleFileProvider;

class Download extends Action
{
    const ENTITY = "company";

    /**
     * @var RawFactory
     */
    protected RawFactory $resultRawFactory;
    /**
     * @var FileFactory
     */
    protected FileFactory $fileFactory;
    /**
     * @var SampleFileProvider
     */
    private SampleFileProvider $sampleFileProvider;

    /**
     * @param RawFactory $resultRawFactory
     * @param FileFactory $fileFactory
     * @param Context $context
     */
    public function __construct(
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        Context $context,
        \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider = null
    ) {
        $this->resultRawFactory      = $resultRawFactory;
        $this->fileFactory           = $fileFactory;
        $this->sampleFileProvider    = $sampleFileProvider
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\ImportExport\Model\Import\SampleFileProvider::class);
        parent::__construct($context);
    }

    /**
     * @return Raw|ResultInterface|void
     */
    public function execute()
    {
        try {
            $fileContents = $this->sampleFileProvider->getFileContents(self::ENTITY);
            $fileSize = $this->sampleFileProvider->getSize(self::ENTITY);
            $fileName = self::ENTITY . '.csv';

            $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                $fileSize
            );

            $result = $this->resultRawFactory->create();
            $result->setContents($fileContents);
        } catch (\Exception $exception){
            // Add your own failure logic here
            $this->messageManager->addErrorMessage($exception->getMessage());
            $result = $this->resultRedirectFactory->create()->setRefererUrl();
        }

        return $result;
    }
}

<?php
namespace WeltPixel\GoogleCards\Controller\Adminhtml\Googlecards;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use WeltPixel\GoogleCards\Model\Config\FileUploader\FileProcessor;

class Saveogimage extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Cms::page';

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @param Context $context
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Context $context,
        FileProcessor $fileProcessor
    ) {
        $this->fileProcessor = $fileProcessor;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->fileProcessor->saveToTmp(key($this->getRequest()->getFiles()->toArray()));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}

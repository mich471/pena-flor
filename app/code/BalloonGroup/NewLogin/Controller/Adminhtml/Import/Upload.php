<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Upload extends Action
{
    /**
     * @var DirectoryList
     */
    protected DirectoryList $directoryList;
    /**
     * @var JsonSerializer
     */
    protected JsonSerializer $jsonSerializer;

    /**
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        JsonSerializer $jsonSerializer
    )
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try{
            $tmpDir = $this->directoryList->getPath('tmp');
            $ext = pathinfo('import_companies_info.csv')['extension'];
            move_uploaded_file($this->getRequest()->getFiles("csv_uploader")['tmp_name'], $tmpDir . "/datasheet-companiesInfo." . $ext);
            // we set response code to 'error' so that an attention message is displayed
            return $this->jsonResponse(['error' => "File uploaded successfully! Now click on import data."]);
        }catch (\Exception $e){
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $response
     * @return mixed
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->jsonSerializer->serialize($response));
    }

}

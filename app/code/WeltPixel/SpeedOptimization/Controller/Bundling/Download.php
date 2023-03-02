<?php

namespace WeltPixel\SpeedOptimization\Controller\Bundling;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use WeltPixel\SpeedOptimization\Model\JsBundlingBuilder;

/**
 * Class Download
 * @package WeltPixel\SpeedOptimization\Controller\Bundling
 */
class Download extends Action
{

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var JsBundlingBuilder
     */
    protected $jsBundlingBuilder;

    /**
     * Download constructor.
     * @param Context $context
     * @param Http $http
     * @param JsBundlingBuilder $jsBundlingBuilder
     */
    public function __construct(
        Context $context,
        Http $http,
        JsBundlingBuilder $jsBundlingBuilderr
    ) {
        parent::__construct($context);
        $this->http = $http;
        $this->jsBundlingBuilder = $jsBundlingBuilderr;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws FileSystemException
     */
    public function execute()
    {
        $fileName = $this->jsBundlingBuilder->getJsBunldingFileName();
        $response = $this->jsBundlingBuilder->getJsBunldingFileContent();

        if ($fileName) {
            $this->getResponse()->setHeader('Content-type', 'application/javascript')
                ->setHeader("Content-Disposition", "attachment; filename=" . $fileName);
            $this->getResponse()->setBody($response);
        }
    }
}

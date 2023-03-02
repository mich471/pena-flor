<?php

namespace WeltPixel\SpeedOptimization\Controller\Bundling;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\SpeedOptimization\Model\JsBundlingBuilder;

/**
 * Class Push
 * @package WeltPixel\SpeedOptimization\Controller\Bundling
 */
class Push extends Action
{

    /**
     * @var JsBundlingBuilder
     */
    protected $jsBundlingBuilder;

    /**
     * Labels constructor.
     * @param Context $context
     * @param JsBundlingBuilder $jsBundlingBuilder
     */
    public function __construct(
        Context $context,
        JsBundlingBuilder $jsBundlingBuilder
    ) {
        parent::__construct($context);
        $this->jsBundlingBuilder = $jsBundlingBuilder;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $themePath = $this->getRequest()->getParam('themePath');
        if (!$themePath) {
            return $this->prepareResult('ok');
        }

        $generateBundleJsFile = $this->getRequest()->getParam('generateJsFile');
        if ($generateBundleJsFile) {
            $result = $this->jsBundlingBuilder->generateAdvancedBundlingJs($themePath);
            if ($result) {
                return $this->prepareResult([
                    'result' => true,
                    'downloadLink' => $result
                ]);
            }
            return $this->prepareResult([
                'result' => false
            ]);
        }

        $pageIdentifier = $this->getRequest()->getParam('pageIdentifier');
        $configOptions = $this->getRequest()->getParam('configOptions');
        if (isset($configOptions)) {
            $this->jsBundlingBuilder->parseBundlingConfigOptions($themePath, $configOptions);
        }

        if ($pageIdentifier) {
            $modulesData = $this->getRequest()->getParam('modules');
            $this->jsBundlingBuilder->parseBundlingPageModules($themePath, $pageIdentifier, $modulesData);
        }

        return $this->prepareResult('ok');
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}

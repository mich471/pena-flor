<?php

namespace WeltPixel\InstagramWidget\Controller\Fetch;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use WeltPixel\InstagramWidget\Model\InstagramWidgetCache;

class Content extends Action
{
    /**
     * @var InstagramWidgetCache
     */
    protected $instagramWidgetCache;

    /**
     * Content constructor.
     * @param Context $context
     * @param InstagramWidgetCache $instagramWidgetCache
     */
    public function __construct(
        Context $context,
        InstagramWidgetCache $instagramWidgetCache
    ) {
        $this->instagramWidgetCache = $instagramWidgetCache;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $cacheId = $this->getRequest()->getParam('cache_id');

        if (!$cacheId) {
            return $this->prepareResult([]);
        }

        $instagramContent = $this->instagramWidgetCache->getInstagramContentByCacheId($cacheId);
        $result['content'] = $instagramContent;

        return $this->prepareResult($result);
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

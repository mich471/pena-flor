<?php

namespace Mascota\CheckoutRedirect\Plugin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Redirect
{
    protected $coreRegistry;

    protected $url;

    protected $resultFactory;

    protected $_logger;

    public function __construct(
        Registry $registry,
        UrlInterface $url,
        ResultFactory $resultFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->coreRegistry = $registry;
        $this->url = $url;
        $this->resultFactory = $resultFactory;
        $this->_logger = $logger;
    }

    public function aroundGetRedirect ($subject, \Closure $proceed)
    {
        if ($this->coreRegistry->registry('has_refer')) {
            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            switch ($this->coreRegistry->registry('has_refer')) {
                case 'checkout':
                    $result->setUrl($this->url->getUrl('checkout'));
                    break;
                default:
                    $result->setUrl($this->url->getUrl());
                    break;
            }
            return $result;
        }
        return $proceed();
    }
}
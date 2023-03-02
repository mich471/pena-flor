<?php

namespace Mascota\CheckoutRedirect\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;

class Register implements ObserverInterface
{
    protected $coreRegistry;

    protected $_request;

    protected $_logger;

    public function __construct(
        Registry $registry,
        Http $request,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->coreRegistry = $registry;
        $this->_request = $request;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($refer = $this->_request->getParam('refer')) {
            $this->coreRegistry->register('has_refer', $refer);
        }
    }
}
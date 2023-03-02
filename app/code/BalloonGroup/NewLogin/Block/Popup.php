<?php
namespace BalloonGroup\NewLogin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as HttpContext;

class Popup extends Template
{
    /**
     * @var ScopeConfigInterface $scopeConfig
    */
    protected $_scopeConfig;

    /**
     * @var Session $customerSession
    */
    protected $_httpContext;

    /**
     * @var Session $customerSession
    */
    //protected $_customerSession;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        HttpContext $httpContext
    )
    {
    	$this->_scopeConfig = $scopeConfig;
    	$this->_httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->_scopeConfig->getValue('newlogin/newlogin/delay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?: 0;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        //return $this->_customerSession->isLoggedIn();
    }
}

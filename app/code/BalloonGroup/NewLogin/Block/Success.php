<?php
namespace BalloonGroup\NewLogin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;

class Success extends Template
{
    /**
     * @var Session
     */
    protected $_customerSession ;

     /**
     * @param Session $customerSession
     */
    public function __construct(
        Session $customerSession
    )
    {
    	$this->_customerSession = $customerSession;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->getRegisterEmail();
    }
}
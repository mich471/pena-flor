<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\EmailDomain;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Edit extends Action
{
    /**
     * @param Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(isset($this->getRequest()->getParams()["email_domain_id"])
            ? __('Edit Email Domain')
            : __('Add New Email Domain'));
        $this->_view->renderLayout();
    }
}

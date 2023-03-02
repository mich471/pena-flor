<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\EmailDomain;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use BalloonGroup\NewLogin\Model\EmailDomain;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action
{
    /**
     * @var EmailDomain
     */
    protected EmailDomain $model;

    /**
     * @param Context $context
     * @param EmailDomain $model
     */
    public function __construct(Context $context, EmailDomain $model)
    {
        parent::__construct($context);
        $this->model = $model;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('email_domain_id');
        $model = $this->model;
        $model->load($id);

        if ($model->getId()) {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Record is deleted successfully!'));
            $this->_redirect('*/emailDomain/index');
        } else {
            $this->messageManager->addErrorMessage(__('Record  not found.'));
            $this->_redirect('*/emailDomain/index');
        }

    }
}

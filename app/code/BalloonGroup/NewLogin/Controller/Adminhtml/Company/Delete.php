<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Company;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use BalloonGroup\NewLogin\Model\Company;

class Delete extends Action
{
    /**
     * @var Company
     */
    protected $_model;

    /**
     * @param Context $context
     * @param Company $model
     */
    public function __construct(Context $context, Company $model)
    {
        parent::__construct($context);
        $this->_model = $model;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('company_id');
        $model = $this->_model;
        $model->load($id);

        if ($model->getId()) {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Record is deleted successfully!'));
            $this->_redirect('*/*/index');
        } else {
            $this->messageManager->addErrorMessage(__('Record  not found.'));
            $this->_redirect('*/*/index');
        }

    }
}

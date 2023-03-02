<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Dni;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use BalloonGroup\NewLogin\Model\Dni;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action
{
    /**
     * @var Dni
     */
    protected Dni $model;

    /**
     * @param Context $context
     * @param Dni $model
     */
    public function __construct(Context $context, Dni $model)
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
        $id = $this->getRequest()->getParam('authorized_dni_id');
        $model = $this->model;
        $model->load($id);

        if ($model->getId()) {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Record is deleted successfully!'));
            $this->_redirect('*/dni/index');
        } else {
            $this->messageManager->addErrorMessage(__('Record  not found.'));
            $this->_redirect('*/dni/index');
        }

    }
}

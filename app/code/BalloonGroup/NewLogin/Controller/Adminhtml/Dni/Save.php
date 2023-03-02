<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Dni;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use BalloonGroup\NewLogin\Model\Dni;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

class Save extends Action
{
    /**
     * @var Session
     */
    protected Session $modelSession;

    /**
     * @var Dni
     */
    protected Dni $model;

    /**
     * @param Context $context
     * @param Session $session
     * @param Dni $model
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        Dni $model
    )
    {
        $this->modelSession = $session;
        $this->model = $model;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->model;
            $id = $this->getRequest()->getParam('authorized_dni_id');

            if ($id) {
                $model->load($id);
            } else {
                unset($data['authorized_dni_id']);
            }

            $model->setData($data);
            $this->_eventManager->dispatch(
                'balloongroup_newlogin_dni_prepare_save',
                ['data' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Record is saved successfully!'));
                $this->modelSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['authorized_dni_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving a record.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['authorized_dni_id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

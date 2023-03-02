<?php
namespace BalloonGroup\NewLogin\Controller\Adminhtml\Company;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use BalloonGroup\NewLogin\Model\Company;
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
     * @var Company
     */
    protected Company $model;

    /**
     * @param Context $context
     * @param Session $session
     * @param Company $model
     */
    public function __construct(
        Action\Context $context,
        Session $session,
        Company $model
    )
    {
        $this->modelSession = $session;
        $this->model = $model;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->model;
            $id = $this->getRequest()->getParam('company_id');

            if ($id) {
                $model->load($id);
            } else {
                unset($data['company_id']);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'balloongroup_newlogin_company_prepare_save',
                ['data' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Record is saved successfully!'));
                $this->modelSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['company_id' => $model->getId(), '_current' => true]);
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

            return $resultRedirect->setPath('*/*/edit', ['company_id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}

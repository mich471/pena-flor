<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Save Tag action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Save extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    const PARAM_CRUD_ID = 'id';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_tagFactory->create();

            if ($id = $this->getRequest()->getParam(static::PARAM_CRUD_ID)) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The tag has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the tag.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                [static::PARAM_CRUD_ID => $this->getRequest()->getParam(static::PARAM_CRUD_ID)]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}

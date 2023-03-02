<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * Edit Tag action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Edit extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_tagFactory->create();

        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Tag no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
      
        $this->_coreRegistry->register('tag', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}

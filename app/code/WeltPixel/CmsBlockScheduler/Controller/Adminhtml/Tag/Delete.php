<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * Delete Tag action
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Delete extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    public function execute()
    {
        $tagId = $this->getRequest()->getParam(static::PARAM_CRUD_ID);
        try {
            $tag= $this->_tagFactory->create()->setId($tagId);
            $tag->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}

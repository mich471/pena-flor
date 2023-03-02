<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * MassDelete action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class MassDelete extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if (!is_array($tagIds) || empty($tagIds)) {
            $this->messageManager->addError(__('Please select tag(s).'));
        } else {
            $tagCollection = $this->_tagCollectionFactory->create()
                ->addFieldToFilter('id', ['in' => $tagIds]);
            try {
                foreach ($tagCollection as $tag) {
                    $tag->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 tag(s) have been deleted.', count($tagIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}

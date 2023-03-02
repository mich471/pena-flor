<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * NewAction
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class NewAction extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}

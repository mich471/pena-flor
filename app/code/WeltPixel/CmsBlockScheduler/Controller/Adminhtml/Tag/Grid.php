<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

/**
 * Tag grid action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Grid extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }
}

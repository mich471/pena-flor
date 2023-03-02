<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportExcel action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class ExportExcel extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    public function execute()
    {
        $fileName = 'cmsblockscheduler.xls';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('WeltPixel\CmsBlockScheduler\Block\Adminhtml\Tag\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}

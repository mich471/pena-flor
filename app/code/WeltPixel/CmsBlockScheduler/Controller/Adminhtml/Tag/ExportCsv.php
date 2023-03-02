<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportCsv action.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class ExportCsv extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    public function execute()
    {
        $fileName = 'cmsblockscheduler_tag.csv';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('WeltPixel\CmsBlockScheduler\Block\Adminhtml\Tag\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}

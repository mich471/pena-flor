<?php

namespace WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * ExportXml action
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class ExportXml extends \WeltPixel\CmsBlockScheduler\Controller\Adminhtml\Tag
{
    public function execute()
    {
        $fileName = 'cmsblockscheduler.xml';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('WeltPixel\CmsBlockScheduler\Block\Adminhtml\Tag\Grid')->getXml();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}

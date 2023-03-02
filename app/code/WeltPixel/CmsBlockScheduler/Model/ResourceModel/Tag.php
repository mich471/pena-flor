<?php

namespace WeltPixel\CmsBlockScheduler\Model\ResourceModel;

/**
 * Tag Resource Model
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Tag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_cmsblockscheduler_tags', 'id');
    }
}

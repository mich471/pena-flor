<?php

namespace WeltPixel\CmsBlockScheduler\Block\Adminhtml;

/**
 * Tag grid container
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Tag extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_tag';
        $this->_blockGroup = 'WeltPixel_CmsBlockScheduler';
        $this->_headerText = __('Tags');
        $this->_addButtonLabel = __('Add New Tag');
        
        parent::_construct();
    }
}

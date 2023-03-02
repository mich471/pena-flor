<?php

namespace WeltPixel\CmsBlockScheduler\Block\Adminhtml\Tag;

/**
 * Tag block edit form container.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'WeltPixel_CmsBlockScheduler';
        $this->_controller = 'adminhtml_tag';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Tag'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init'  => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ],
            ],
            10
        );


        if ($this->getRequest()->getParam('saveandclose')) {
            $this->_formScripts[] = 'window.close();';
        }
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back'     => 'edit',
                'tab'      => '{{tab_id}}',
                'store'    => $this->getRequest()->getParam('store'),
                'id'       => $this->getRequest()->getParam('id'),
                'current_tag_id' => $this->getRequest()->getParam('current_tag_id'),
            ]
        );
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function getSaveAndCloseWindowUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back'     => 'edit',
                'tab'      => '{{tab_id}}',
                'store'    => $this->getRequest()->getParam('store'),
                'id'       => $this->getRequest()->getParam('id'),
                'current_tag_id' => $this->getRequest()->getParam('current_tag_id'),
                'saveandclose'      => 1,
            ]
        );
    }
}

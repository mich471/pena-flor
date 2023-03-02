<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\Field\Edit\Button;

class Delete extends Generic
{
    public function getButtonData()
    {
        if ($this->isAllowed()) {
            return [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm('
                    . '"' . __('Are you sure you want to do this?') . '",'
                    . '"' . $this->getUrl('*/*/delete', ['id' => $this->getCurrentId()]) . '"'
                    . ')',
                'sort_order' => 20,
            ];
        }

        return parent::getButtonData();
    }
}

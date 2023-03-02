<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Block\Adminhtml\Rate;

class Label extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('shipping_method', [
            'label' => __('Shipping Method'),
        ]);
        $this->addColumn('label', [
            'label' => __('Label')
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

}

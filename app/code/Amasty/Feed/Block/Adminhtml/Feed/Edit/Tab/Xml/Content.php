<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\Feed\Edit\Tab\Xml;

class Content extends \Amasty\Feed\Block\Adminhtml\Feed\Edit\Tab\Content
{
    public const CONDITION_BASED_ATTRIBUTES_KEY = 'condition_based_attributes';

    /**
     * @var string
     */
    protected $_template = 'feed/xml.phtml';

    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'label' => __('Insert'),
                'id' => 'insert_button',
                'class' => 'add'
            ]
        );

        $this->setChild('insert_button', $button);

        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'label' => __('Update'),
                'id' => 'update_button',
                'class' => 'add hidden'
            ]
        );

        $this->setChild('update_button', $button);

        return parent::_prepareLayout();
    }

    public function getInsertButtonHtml()
    {
        return $this->getChildHtml('insert_button');
    }

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    public function escapeHtmlInContent($value)
    {
        if ($value) {
            //phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
            $html = htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8');

            return $this->escapeHtml($html);
        }

        return '';
    }

    public function getAttributeOptions(): array
    {
        $attributeOptions = parent::getAttributeOptions();
        if ($this->getData('is_merged_attributes')) {
            unset($attributeOptions[self::CONDITION_BASED_ATTRIBUTES_KEY]);
        }

        return $attributeOptions;
    }
}

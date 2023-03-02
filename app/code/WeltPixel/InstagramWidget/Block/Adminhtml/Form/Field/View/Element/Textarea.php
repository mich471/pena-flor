<?php
namespace WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\View\Element;

/**
 * Class Textarea
 * @package WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\View\Element
 */
Class Textarea extends  \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Render HTML
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<textarea name="' .
            $this->getInputName() .
            '" id="' .
            $this->getInputId() .
            '" class="' .
            $this->getClass() .
            '" title="' .
            $this->escapeHtml($this->getTitle()) .
            '" ' .
            $this->getExtraParams() .
            '>';
        $html .= $this->getValue();
        $html .= '</textarea>';
        return $html;
    }

    /**
     * Alias for toHtml()
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}

<?php
namespace WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field;

use Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class InstagramToken
 * @package WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field
 */
Class InstagramToken extends AbstractFieldArray
{

    const TOKEN_PATH = 'weltpixel_instagram/general/tokens';
    const TOKEN_INFO_PATH = 'weltpixel_instagram/general/tokens_info';

    /**
     * @var \WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\View\Element\Textarea
     */
    protected $_tokenRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn('token_name', ['label' => __('Token Name'), 'class' => 'required-entry admin__control-text']);
        $this->addColumn('token_value', [
            'label' => __('Token Value'),
            'size' => 100,
            'class' => 'required-entry admin__control-text',
            'renderer' => $this->_getTokenRender()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Token');
    }


    protected function _getTokenRender()
    {
        if (!$this->_tokenRenderer) {
            $this->_tokenRenderer = $this->getLayout()->createBlock(
                \WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\View\Element\Textarea::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_tokenRenderer->setClass('required-entry admin__control-text');
        }

        return $this->_tokenRenderer;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);
        $tokenInfo = $this->_scopeConfig->getValue(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_INFO_PATH);
        $genericDescription = "This section stores all your Instagram tokens. All tokens are automatically refreshed every month.<br/><br/>
These tokens can be used to insert WeltPixel Instagram Widgets into CMS Pages or Blocks. Configuration options are found at the WeltPixel Instagram Widget level in CMS Pages or Blocks when using the <b>Insert Widget functionality</b>. <br/><br/>";

        if ($tokenInfo) {
            $html .= '<div style="border: 1px solid #c3c3c3; padding: 15px;">' .
                $genericDescription . $tokenInfo . '</div>';
        }

        return $html;
    }
}

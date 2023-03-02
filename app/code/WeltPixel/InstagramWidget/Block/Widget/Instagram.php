<?php
namespace WeltPixel\InstagramWidget\Block\Widget;

class Instagram extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    /**
     * Instagram constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $_serializer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $_serializer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_serializer = $_serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $instagramApiType = $this->getData('instagram_api_type');
        switch ($instagramApiType) {
            case 'basic_api':
                $template = 'widget/basic/instagram_widget.phtml';
                break;
            case 'javascript_parser':
                $template = 'widget/js/instagram_widget.phtml';
                break;
            default:
                $template = 'widget/instagram_widget.phtml';
                break;
        }

        $this->setTemplate($template);
        return parent::getTemplate();
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isLazyLoadEnabled() {
        return $this->_scopeConfig->getValue('weltpixel_lazy_loading/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed|string
     */
    public function getLazyLoadPlaceholderWidth() {
        $imgWidth = null;
        $imgWidth = (int) $this->_scopeConfig->getValue('weltpixel_lazy_loading/advanced/placeholder_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $imgWidth && is_integer($imgWidth) ? $imgWidth . 'px' : 'auto';
    }

    public function getInstagramToken() {
        $token = $this->getData('token');
        if ($this->getData('use_predefined_token')) {
            $tokenId = $this->getData('predefined_token');
            $tokenOptions = $this->_scopeConfig->getValue(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_PATH);
            if (isset($tokenOptions)) {
                $tokens = $this->_serializer->unserialize($tokenOptions);
                foreach ($tokens as $tokenOpt) {
                    if ($tokenId == preg_replace('/[^A-Za-z0-9\-]/', '', $tokenOpt['token_name'])) {
                        $token = $tokenOpt['token_value'];
                    }
                }
            }
        }

        return $token;
    }
}

<?php
namespace WeltPixel\InstagramWidget\Model\Config\Source;

class Tokens implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Tokens constructor.
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
    )
    {
        $this->_serializer = $serializer;
        $this->_scopeConfig = $_scopeConfig;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $tokenOptions = $this->_scopeConfig->getValue(\WeltPixel\InstagramWidget\Block\Adminhtml\Form\Field\InstagramToken::TOKEN_PATH);
        if (isset($tokenOptions)) {
            $tokens = $this->_serializer->unserialize($tokenOptions);
            foreach ($tokens as $tokenOpt) {
                $result[preg_replace('/[^A-Za-z0-9\-]/', '', $tokenOpt['token_name'])] = $tokenOpt['token_name'];
            }
        }
        return $result;
    }
}

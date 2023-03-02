<?php
namespace BalloonGroup\NewLogin\Block\Adminhtml\System\Config\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Genericbutton
{
    protected $_urlBuilder;

    protected $_registry;

    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_registry = $registry;
    }

    public function getId()
    {
        $contact = $this->_registry->registry('data');
        return $contact ? $contact->getId() : null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
}
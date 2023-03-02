<?php

namespace WeltPixel\Quickview\Plugin;

use Magento\Framework\Message\MessageInterface;

class RemoveCartMessagesFromRenderer
{
    /**
     * @var \WeltPixel\Quickview\Helper\Data $helper
     */
    protected $helper;


    /**
     * RemoveCartMessagesFromRenderer constructor.
     * @param \WeltPixel\Quickview\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\Quickview\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\View\Element\Message\Renderer\BlockRenderer $subject
     * @param $result
     * @param MessageInterface $message
     * @param array $initializationData
     * @return mixed
     */
    public function afterRender(
        \Magento\Framework\View\Element\Message\Renderer\BlockRenderer $subject,
        $result,
        MessageInterface $message,
        array $initializationData
    ) {
        if (!$this->helper->isAjaxCartEnabled()) {
            return $result;
        }

        $messageIdentifier = $message->getIdentifier();
        if ($messageIdentifier == 'addCartSuccessMessage') {
            return '';
        }

        return $result;
    }
}

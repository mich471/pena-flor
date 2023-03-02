<?php
namespace WeltPixel\ThankYouPage\Block\Multishipping;

/**
 * Class Newsletter
 * @package WeltPixel\ThankYouPage\Block\Multishipping
 */
class Newsletter extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscriber;

    /**
     * @var \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected $multishipping;

    /**
     * @var \WeltPixel\ThankYouPage\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \WeltPixel\ThankYouPage\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \WeltPixel\ThankYouPage\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->multishipping = $multishipping;
        $this->subscriber = $subscriber;
    }

    /**
     * Retrieve current email address
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getEmailAddress()
    {
        return $this->multishipping->getCustomer()->getEmail();
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('wpthankyoupage/newsletter/subscribe', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->helper->getNewsletterSubscribeDescription();
    }

    /**
     * Render the content of the block only if user is not subscribed
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $subscriberModel = $this->subscriber->loadByEmail($this->getEmailAddress());

        if ($subscriberModel->isSubscribed()) {
            return '';
        }

        return parent::_toHtml();
    }
}

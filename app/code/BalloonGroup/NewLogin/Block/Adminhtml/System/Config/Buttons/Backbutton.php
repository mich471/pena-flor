<?php
namespace BalloonGroup\NewLogin\Block\Adminhtml\System\Config\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Backbutton extends Genericbutton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
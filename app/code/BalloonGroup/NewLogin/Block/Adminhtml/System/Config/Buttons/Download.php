<?php
namespace BalloonGroup\NewLogin\Block\Adminhtml\System\Config\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Download extends Genericbutton implements ButtonProviderInterface
{

    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id'        => 'download_companies_info',
                'label'     => __('Download sample file')
            ]);
        return $button->toHtml();
    }

    public function getButtonData()
    {
        return [
            'label' => __('Download sample file'),
            'class' => 'action-secondary',
            'on_click' => sprintf("location.href = '%s';", $this->getDownloadUrl()),
            'sort_order' => 10
        ];
    }

    public function getDownloadUrl()
    {
        return $this->getUrl('newlogin/import/download');
    }
}


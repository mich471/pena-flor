<?php
namespace BalloonGroup\NewLogin\Block\Adminhtml\System\Config\Buttons;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Import extends Genericbutton implements ButtonProviderInterface
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id'        => 'import_companies_info',
                'label'     => __('Import')
            ]);
        return $button->toHtml();
    }

    protected function path()
    {
        return $this->getUrl('newlogin/import/import');
    }

    public function getButtonData()
    {
        return [
            'label' => __('Import'),
            'class' => 'import primary',
            'on_click' => sprintf("location.href = '%s';", $this->path()),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'import']],
                'form-role' => 'import',
            ],
            'sort_order' => 90,
        ];
    }
}
<?php

namespace Formax\Countdown\Block\Adminhtml\Widget;

class Date extends \Magento\Backend\Block\Template {

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        
        $input = $this->_elementFactory->create('text', ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->addClass('admin__control-text');

        $opts = $this->getCalendarParameters();
        $opts->setElementId($element->getId());

        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $html = $input->getElementHtml() . $this->getScriptHtml($opts);

        $element->setData('after_element_html', $html);
        return $element;
    }

    /**
     * Get calendar parameters
     * 
     * @return \Magento\Framework\DataObject
     */
    private function getCalendarParameters() {
        $opts = new \Magento\Framework\DataObject();
        $opts->setDateFormat(\Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT);
        $opts->setTimeFormat('HH:mm:ss');
        $opts->setShowsTime(true);
        $opts->setSelectText(__('Select Date'));
        return $opts;
    }

    /**
     * Get script html
     * 
     * @param \Magento\Framework\DataObject
     * @return string
     */
    public function getScriptHtml($opts) {
        return 
        '<script>
            require([
                "jquery", "mage/calendar"
            ], function($){
                $("#' .$opts->getElementId(). '").calendar({
                    buttonText: "' . $opts->getSelectText() . '",
                    dateFormat: "' . $opts->getDateFormat() . '",
                    showsTime: ' . $opts->getShowsTime() . ',
                    timeFormat: "' . $opts->getTimeFormat() . '"
                });
            });
        </script>';
    }

}
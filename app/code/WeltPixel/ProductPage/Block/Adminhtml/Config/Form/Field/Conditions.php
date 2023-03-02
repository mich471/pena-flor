<?php
namespace WeltPixel\ProductPage\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Conditions
 * @package WeltPixel\ProductPage\Block\Adminhtml\Config\Form\Field
 */
class Conditions extends Field
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $input;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        Context $context,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->conditions = $conditions;
        $this->rule = $rule;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * Get Month and Day Element
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        try {
            $elementValue = $this->serializer->unserialize($element->getValue());
        } catch (\Exception $ex) {
            $elementValue = [];
        }

        $this->rule->getConditions()->setJsFormObject($element->getHtmlId());
        $this->rule->loadPost($elementValue);

        $this->input = $this->elementFactory->create('text');
        $this->input->setRule($this->rule)->setRenderer($this->conditions);
        $inputHtml = $this->input->toHtml();

        $newChildUrl = $this->getUrl(
            'catalog_widget/product_widget/conditions/form/' . $element->getHtmlId()
        );

        return $this->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('WeltPixel_ProductPage::system/config/conditions.phtml')
            ->setConditionInputHtml($inputHtml)
            ->setNewChildUrl($newChildUrl)
            ->setHtmlId($element->getHtmlId())
            ->toHtml();
    }
}

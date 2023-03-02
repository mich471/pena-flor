<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field;

use Amasty\Feed\Block\Adminhtml\Feed\Edit\Tab\Xml\Content;
use Amasty\Feed\Block\Adminhtml\Field\Edit\Conditions;
use Amasty\Feed\Model\Config\Source\CustomFieldType;
use Amasty\Feed\Model\Field\Utils\FieldNameResolver;
use Amasty\Feed\Ui\Component\Form\ProductAttributeOptions;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Math\Random;
use Magento\Framework\View\LayoutInterface;

class FormProcessor
{
    /**
     * @var ProductAttributeOptions
     */
    private $attributeOptions;

    /**
     * @var CustomFieldType
     */
    private $customFieldType;

    /**
     * @var FieldNameResolver
     */
    private $fieldNameResolver;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var string
     */
    private $formNamespace = Conditions::FORM_NAMESPACE;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $elementIds = [];

    public function __construct(
        ProductAttributeOptions $attributeOptions,
        CustomFieldType $customFieldType,
        FieldNameResolver $fieldNameResolver,
        Random $random
    ) {
        $this->attributeOptions = $attributeOptions;
        $this->customFieldType = $customFieldType;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->random = $random;
    }

    public function initialize(
        LayoutInterface $layout,
        string $type = FieldNameResolver::TYPE_BY_CONDITIONS
    ): void {
        $this->layout = $layout;
        $this->type = $type;
    }

    public function execute(Form $form, array $values): string
    {
        $dependencies = $this->layout->createBlock(Dependence::class);
        $fieldset = $this->getFieldset($form);

        $fieldset->addField(
            $this->formatResultFieldName('[entity_type]'),
            'select',
            [
                'name' => $this->formatRuleFieldName('[result][entity_type]'),
                'label' => __('Type'),
                'title' => __('Type'),
                'options' => $this->customFieldType->toArray(),
                'data-form-part' => $this->formNamespace
            ]
        );

        $fieldset->addField(
            $this->formatResultFieldName('[attribute]'),
            'select',
            [
                'name' => $this->formatRuleFieldName('[result][attribute]'),
                'label' => __('Attribute'),
                'title' => __('Attribute'),
                'options' => $this->attributeOptions->getOptionsForBlock(),
                'data-form-part' => $this->formNamespace,
                'note' => __("If you can't find the needed attribute in the list, please edit the needed attribute.
                Open the 'Storefront Properties' tab in the attribute edit menu and set
                'Use for Promo Rule Conditions' field to 'YES'.")
            ]
        );

        $fieldset->addField(
            $this->formatResultFieldName('[modify]'),
            'text',
            [
                'name' => $this->formatRuleFieldName('[result][modify]'),
                'label' => __('Modification'),
                'title' => __('Modification'),
                'placeholder' => __('Percentage (like +15%), or fixed value (like -20)'),
                'data-form-part' => $this->formNamespace,
            ]
        );

        $fieldset->addField(
            $this->formatResultFieldName('[custom_text]'),
            'text',
            [
                'name' => $this->formatRuleFieldName('[result][custom_text]'),
                'label' => __('Custom Text'),
                'title' => __('Custom Text'),
                'data-form-part' => $this->formNamespace,
            ]
        );

        $fieldset->addField(
            $this->formatResultFieldName('[merged_text]'),
            'text',
            [
                'name' => $this->formatRuleFieldName('[result][merged_text]'),
                'label' => __('Custom Text'),
                'title' => __('Custom Text'),
                'data-form-part' => $this->formNamespace,
                'value' => ''
            ]
        );

        $form->getElement(
            $this->formatResultFieldName('[merged_text]')
        )->setRenderer(
            $this->layout->createBlock(Content::class)
                ->setData([
                    'is_merged_attributes' => true,
                    'data-form-part' => $this->formNamespace,
                    'element-id' => $this->generateElementId()
                ])
        );

        $dependencies->addFieldMap(
            $this->formatResultFieldName('[entity_type]'),
            $this->formatResultFieldName('[entity_type]')
        )->addFieldMap(
            $this->formatResultFieldName('[attribute]'),
            $this->formatResultFieldName('[attribute]')
        )->addFieldMap(
            $this->formatResultFieldName('[modify]'),
            $this->formatResultFieldName('[modify]')
        )->addFieldMap(
            $this->formatResultFieldName('[custom_text]'),
            $this->formatResultFieldName('[custom_text]')
        )->addFieldMap(
            $this->formatResultFieldName('[merged_text]'),
            $this->formatResultFieldName('[merged_text]')
        )->addFieldDependence(
            $this->formatResultFieldName('[attribute]'),
            $this->formatResultFieldName('[entity_type]'),
            CustomFieldType::ATTRIBUTE
        )->addFieldDependence(
            $this->formatResultFieldName('[modify]'),
            $this->formatResultFieldName('[entity_type]'),
            CustomFieldType::ATTRIBUTE
        )->addFieldDependence(
            $this->formatResultFieldName('[custom_text]'),
            $this->formatResultFieldName('[entity_type]'),
            CustomFieldType::CUSTOM_TEXT
        )->addFieldDependence(
            $this->formatResultFieldName('[merged_text]'),
            $this->formatResultFieldName('[entity_type]'),
            CustomFieldType::MERGED_ATTRIBUTES
        );

        $form->setValues($values);

        return $form->toHtml() . $dependencies->toHtml();
    }

    private function getFieldset(Form $form): Fieldset
    {
        if ($this->type == FieldNameResolver::TYPE_DEFAULT) {
            return $form->addFieldset('default', [
                'comment' => __('Default value will be used if none of the conditions apply.')
            ]);
        }

        return $form->addFieldset('result', ['legend' => __('Output Value')]);
    }

    private function formatRuleFieldName(string $fieldName): string
    {
        return $this->fieldNameResolver->getRuleFieldName($this->type) . $fieldName;
    }

    private function formatResultFieldName(string $fieldName): string
    {
        return $this->fieldNameResolver->getResultFieldName($this->type) . $fieldName;
    }

    private function generateElementId(): string
    {
        if (!isset($this->elementIds[$this->type])) {
            $this->elementIds[$this->type] = 'elemId' . $this->random->getRandomString(10);
        }

        return $this->elementIds[$this->type];
    }
}

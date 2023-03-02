<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\Field\Edit;

use Amasty\Feed\Model\Field\ConditionProvider;
use Amasty\Feed\Model\Field\FormProcessor;
use Amasty\Feed\Model\Field\FormProcessorFactory;
use Amasty\Feed\Model\Field\Utils\FieldNameResolver;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Defaults extends Generic
{
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var FormProcessor
     */
    private $formProcessor;

    /**
     * @var ConditionProvider
     */
    private $conditionProvider;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ProductMetadataInterface $metadata,
        FormProcessorFactory $formProcessorFactory,
        ConditionProvider $conditionProvider,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->metadata = $metadata;
        $this->formProcessor = $formProcessorFactory->create();
        $this->formProcessor->initialize($this->getLayout(), FieldNameResolver::TYPE_DEFAULT);
        $this->conditionProvider = $conditionProvider;
    }

    public function toHtml(): string
    {
        if (version_compare($this->metadata->getVersion(), '2.2.0', '>=')
            && !$this->getLayout()
        ) {
            //Fix for Magento >2.2.0 to display right form layout.
            //Result of compatibility with 2.1.x.
            $this->_prepareLayout();
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $condition = $this->conditionProvider->getCondition(
            (int)$this->getRequest()->getParam('id'),
            FieldNameResolver::TYPE_DEFAULT
        );

        return $this->formProcessor->execute($form, $condition->getData());
    }
}

<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Export\Product as ExportProduct;
use Amasty\Feed\Model\Field\ResourceModel\Collection as FieldCollection;
use Amasty\Feed\Model\Field\ResourceModel\CollectionFactory as FieldCollectionFactory;

class CustomFieldsAttribute implements CustomOptionSourceInterface
{
    /**
     * @var Utils\ArrayCustomizer
     */
    private $arrayCustomizer;

    /**
     * @var FieldCollection
     */
    private $fieldCollection;

    public function __construct(
        Utils\ArrayCustomizer $arrayCustomizer,
        FieldCollectionFactory $fieldCollectionFactory
    ) {
        $this->arrayCustomizer = $arrayCustomizer;
        $this->fieldCollection = $fieldCollectionFactory->create();
    }

    public function getOptions(): array
    {
        return $this->arrayCustomizer->customizeArray(
            $this->fieldCollection->getSortedCollection()->getData(),
            ExportProduct::PREFIX_CUSTOM_FIELD_ATTRIBUTE,
            false
        );
    }
}

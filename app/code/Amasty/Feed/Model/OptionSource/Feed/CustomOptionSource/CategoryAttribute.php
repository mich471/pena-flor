<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\OptionSource\Feed\CustomOptionSource;

use Amasty\Feed\Model\Category\ResourceModel\Collection as CategoryCollection;
use Amasty\Feed\Model\Category\ResourceModel\CollectionFactory as CategoryCollectionFactory;
use Amasty\Feed\Model\Export\Product as ExportProduct;

class CategoryAttribute implements CustomOptionSourceInterface
{
    /**
     * @var Utils\ArrayCustomizer
     */
    private $arrayCustomizer;

    /**
     * @var Utils\OptionFormatter
     */
    private $optionFormatter;

    /**
     * @var CategoryCollection
     */
    private $categoryCollection;

    public function __construct(
        Utils\ArrayCustomizer $arrayCustomizer,
        Utils\OptionFormatter $optionFormatter,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->arrayCustomizer = $arrayCustomizer;
        $this->optionFormatter = $optionFormatter;
        $this->categoryCollection = $categoryCollectionFactory->create()->addOrder('name');
    }

    public function getOptions(): array
    {
        $attributes[
            $this->optionFormatter->getCode('category', ExportProduct::PREFIX_CATEGORY_ATTRIBUTE)
        ] = $this->optionFormatter->getTitle((string)__('Default'), 'category');
        $attributes += $this->arrayCustomizer->customizeArray(
            $this->categoryCollection->getData(),
            ExportProduct::PREFIX_MAPPED_CATEGORY_ATTRIBUTE,
            false
        );

        return $attributes;
    }
}

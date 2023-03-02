<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category\DataProvider;

use Amasty\Feed\Model\Category\ResourceModel\CollectionFactory;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\Feed\Model\Category\ResourceModel\Collection
     */
    protected $collection;

    public function __construct(
        CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}

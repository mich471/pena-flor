<?php
namespace WeltPixel\SmartProductTabs\Model\SmartProductTabs;

use WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \WeltPixel\SmartProductTabs\Model\ResourceModel\SmartProductTabs\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $smartProductTabsCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $smartProductTabsCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $smartProductTabsCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \WeltPixel\SmartProductTabs\Model\SmartProductTabs $item */
        foreach ($items as $item) {
            $itemData = $item->getData();
            $this->loadedData[$item->getId()] = $itemData;
        }

        $data = $this->dataPersistor->get('weltpixel_smartproducttab');
        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('weltpixel_smartproducttab');
        }

        return $this->loadedData;
    }
}

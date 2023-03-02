<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field\DataProvider;

use Amasty\Feed\Block\Adminhtml\Field\Edit\Conditions;
use Amasty\Feed\Model\Field\ResourceModel\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     *
     * Used to process data of fieldset "field_general"
     */
    public function getData(): array
    {
        $data = [];
        $item = parent::getData()['items'][0] ?? null;
        if ($item) {
            $data[$item[$this->primaryFieldName]] = $item;
        }
        $this->restoreUnsavedData($data);

        return $data;
    }

    /**
     * Try to get unsaved data if error was occurred.
     *
     * @param array $data
     */
    private function restoreUnsavedData(array &$data)
    {
        $tempData = $this->dataPersistor->get(Conditions::FORM_NAMESPACE);
        if ($tempData) {
            /** @var \Amasty\Feed\Model\Field\Field $tempModel */
            $tempModel = $this->collection->getNewEmptyItem();
            $tempModel->setData($tempData);
            $data[$tempModel->getId()] = $tempModel->getData();
            $this->dataPersistor->clear(Conditions::FORM_NAMESPACE);
        }
    }
}

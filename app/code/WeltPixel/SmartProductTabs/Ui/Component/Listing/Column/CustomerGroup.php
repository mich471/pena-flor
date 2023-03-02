<?php
namespace WeltPixel\SmartProductTabs\Ui\Component\Listing\Column;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class CustomerGroup
 * @package WeltPixel\SmartProductTabs\Ui\Component\Listing\Column
 */
class CustomerGroup extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        GroupCollectionFactory $groupCollectionFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if ($this->options === null) {
            $this->options = $this->groupCollectionFactory->create()->toOptionHash();
        }

        $customerGroupNames = $this->options;
        $customerGroupdIds = explode(",", $item['customer_group']);

        if (count($this->options) == count($customerGroupdIds)) {
            return 'All Customer Groups';
        }

        array_walk($customerGroupdIds, function (&$item, $key) use ($customerGroupNames) {
            $item = $customerGroupNames[$key];
        });

        $content = implode(", ", $customerGroupdIds);
        return $content;
    }
}

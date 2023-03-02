<?php
namespace WeltPixel\Sitemap\Ui\Component\Listing\Column;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class IndexFollow
 */
class IndexFollow extends Column
{
    /**
     * @var \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder
     */
    protected $indexFollowBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->indexFollowBuilder = $indexFollowBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $items
     * @return array
     */
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
        $indexFollowEnabled = $item['wp_enable_index_follow'];
        if (!$indexFollowEnabled) {
            return ' - ';
        }

        $indexValue = $item['wp_index_value'];
        $followValue = $item['wp_follow_value'];

        return  $this->indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);

    }
}

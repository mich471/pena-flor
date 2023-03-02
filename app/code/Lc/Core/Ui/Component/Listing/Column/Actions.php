<?php

namespace Lc\Core\Ui\Component\Listing\Column;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        LoggerInterface $logger,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $data = $this->getData();
        if (isset($dataSource['data']['items'])) {

            $routePath = null;
            if ( isset($data['config']['routePath']) ) {
                $routePath = $data['config']['routePath'];
            }

            $fieldName = null;
            if ( isset($data['config']['fieldName']) ) {
                $fieldName = $data['config']['fieldName'];
            }

            $storeId = $this->context->getFilterParam('store_id');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        (is_null($routePath)) ? '/edit' : $routePath."/edit",
                        ['id' => $item[ is_null( $fieldName ) ? 'entity_id' : $fieldName ], 'store' => $storeId]
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        (is_null($routePath)) ? '/delete' : $routePath."/delete",
                        ['id' => $item[ is_null( $fieldName ) ? 'entity_id' : $fieldName ], 'store' => $storeId]
                    ),
                    'label' => __('Delete'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}

<?php
namespace WeltPixel\SmartProductTabs\Ui\Component;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->filterGroupBuilder = $filterGroupBuilder;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (in_array($filter->getField(), ['customer_group'])) {
            $customerGroups = $filter->getValue();
            foreach ($customerGroups as $customerGroup) {
                $filter = $this->filterBuilder->setField('customer_group')
                    ->setValue($customerGroup)
                    ->setConditionType('finset')
                    ->create();
                $this->searchCriteriaBuilder->addFilter($filter);
            }
        } elseif (in_array($filter->getField(), ['store_id'])) {
            $filter->setConditionType('finset');
        } else {
            $this->searchCriteriaBuilder->addFilter($filter);
        }
    }
}

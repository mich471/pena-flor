<?php
namespace BalloonGroup\NewLogin\Ui\DataProvider\EmailDomain\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;


class Collection extends SearchResult
{

    /**
     * @return Collection
     */
    protected function _initSelect(): Collection
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['bnc' => $this->getTable('balloon_newlogin_companies')],
            'main_table.company_id = bnc.company_id',
            ['company']
        );

        $this->addFilterToMap('company', 'bnc.company');

        return $this;
    }
}

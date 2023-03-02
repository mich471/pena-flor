<?php
namespace BalloonGroup\NewLogin\Ui\DataProvider\Company\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;


class Collection extends SearchResult
{

    /**
     * @return void
     */
    protected function _initSelect()
      {
          $this->addFilterToMap('company_id', 'main_table.company_id');
          parent::_initSelect();
      }
}

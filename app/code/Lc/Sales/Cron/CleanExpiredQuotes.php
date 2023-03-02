<?php

namespace Lc\Sales\Cron;

use Magento\Sales\Cron\CleanExpiredQuotes as CoreCleanExpiredQuotes;

/**
 * Created by PhpStorm.
 * User: jgimenez
 * Date: 08/11/2017
 * Time: 13:07
 *
 * DISABLED FOR TEST
 */
class CleanExpiredQuotes extends CoreCleanExpiredQuotes
{

    /**
     * Clean expired quotes (cron process)
     *
     * @return void
     */
    public function execute()
    {
        $lifetimes = $this->storesConfig->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId => $lifetime) {
            $lifetime *= self::LIFETIME;

            /** @var $quotes \Magento\Quote\Model\ResourceModel\Quote\Collection */
            $quotes = $this->quoteCollectionFactory->create();

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('created_at', ['to' => date("Y-m-d", time() - $lifetime)]);
            $quotes->addFieldToFilter('is_active', 0);

            foreach ($this->getExpireQuotesAdditionalFilterFields() as $field => $condition) {
                $quotes->addFieldToFilter($field, $condition);
            }

            $quotes->walk('delete');
        }
    }

}
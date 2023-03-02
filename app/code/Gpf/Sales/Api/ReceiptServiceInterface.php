<?php
/**
 * Created by PhpStorm.
 * User: jgimenez
 * Date: 12/07/2018
 * Time: 12:51
 */

namespace Gpf\Sales\Api;

use Gpf\Sales\Api\Data\ReceiptInterface;
use Magento\Sales\Api\Data\OrderInterface;

interface ReceiptServiceInterface
{
    /**
     * @param OrderInterface $order
     * @return ReceiptInterface
     */
    public function getReceiptForOrder(OrderInterface $order);

}

<?php
namespace WeltPixel\ReviewsWidget\Controller\Product;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;

class ListAjax extends \Magento\Review\Controller\Product\ListAjax
{
    /**
     * Show list of product's reviews
     *
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        return parent::execute();
    }
}

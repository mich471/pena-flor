<?php

namespace WeltPixel\ReviewsWidget\Plugin;

class ReviewsView
{
    /**
     *
     * @var  \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \WeltPixel\ReviewsWidget\Helper\Data $helper
     */
    protected $helper;


    /**
     * @param  \Magento\Framework\App\Request\Http $request
     * @param \WeltPixel\ReviewsWidget\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \WeltPixel\ReviewsWidget\Helper\Data $helper
        ) {
        $this->request = $request;
        $this->helper = $helper;
    }


    /**
     * @param \Magento\Review\Block\Product\View $subject
     * @param $result
     * @return mixed
     */
    public function afterGetReviewsCollection(
        \Magento\Review\Block\Product\View $subject, $result)
    {
        $requestAction = $this->request->getFullActionName();
        if ($requestAction == 'reviewswidget_product_listAjax') {
            $itemsLimit = $this->helper->getProductLimit();
            if ($itemsLimit && $itemsLimit > 0) {
                $result->setCurPage(1)->setPageSize($itemsLimit);
            };
        }

        return $result;
    }
}

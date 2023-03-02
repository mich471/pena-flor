<?php

namespace WeltPixel\ReviewsWidget\Block\Widget;

use WeltPixel\ReviewsWidget\Model\Config\Source\UrlLink;

/**
 * Class ReviewSummary
 * @package WeltPixel\ReviewsWidget\Block\Widget
 */
class ReviewSummary extends \Magento\Review\Block\Product\ReviewRenderer implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'WeltPixel_ReviewsWidget::widget/helper/summary.phtml',
    ];

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/reviewsummary.phtml');
    }

    public function isReviewEnabled() : string
    {
        return true;
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        return $this->getData('product_id');
    }

    /**
     * @return bool
     */
    public function showAddYourReviewLink()
    {
        return (bool)$this->getData('show_add_your_review_link');
    }

    /**
     * @return bool
     */
    public function showIfNoReviews()
    {
        return (bool)$this->getData('show_if_no_reviews');
    }

    /**
     * @return bool
     *
     */
    public function getDisplayIfEmpty()
    {
        return $this->showIfNoReviews();
    }

    /**
     * @return string
     */
    public function getUrlLink()
    {
        return $this->getData('url_link');
    }

    /**
     * @return string
     */
    public function getCustomUrlLink()
    {
        return $this->getData('custom_url_link');
    }

    /**
     * @param bool $useDirectLink
     * @return string
     */
    public function getReviewsUrl($useDirectLink = false)
    {
        $urlLinkType = $this->getUrlLink();
        $reviewsUrl = '';
        switch ($urlLinkType) {
            case UrlLink::OPTION_CURRENTPAGE:
                $reviewsUrl = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
                break;
            case UrlLink::OPTION_PRODUCTPAGE:
                $reviewsUrl = parent::getReviewsUrl($useDirectLink);
                break;
            case UrlLink::OPTION_CUSTOMURL:
                $reviewsUrl = $this->getCustomUrlLink();
                break;
        }
        return $reviewsUrl;
    }

    /**
     * @return string
     */
    public function getReviewsElementId()
    {
        $result = '#reviews';
        $urlLinkType = $this->getUrlLink();
        if ($urlLinkType == UrlLink::OPTION_CURRENTPAGE) {
            $result = '#review-tab';
        }

        return $result;
    }

}

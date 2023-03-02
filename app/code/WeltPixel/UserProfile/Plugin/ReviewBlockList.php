<?php

namespace WeltPixel\UserProfile\Plugin;

use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use WeltPixel\UserProfile\Helper\Data as UserProfileHelper;

class ReviewBlockList
{

    /**
     * @var ReviewCollection
     */
    protected $reviewsCollection;

    /**
     * @var UserProfileHelper
     */
    protected $userProfileHelper;

    public function __construct(
        UserProfileHelper $userProfileHelper
    )
    {
        $this->userProfileHelper = $userProfileHelper;
        $this->reviewsCollection = null;
    }

    /**
     * @param \Magento\Review\Block\Product\View\ListView $subject
     * @param ReviewCollection $result
     * @return ReviewCollection
     */
    public function afterGetReviewsCollection(
        \Magento\Review\Block\Product\View\ListView $subject,
        $result
    )
    {
        if ($this->userProfileHelper->isEnabled() && $this->userProfileHelper->isReviewsProfileEnabled()) {
            if (null === $this->reviewsCollection) {
                $result->getSelect()->joinLeft(
                    ['wpuserprofile' => $result->getTable('weltpixel_user_profile')],
                    'detail.customer_id=wpuserprofile.customer_id',
                    ['avatar', 'username', 'profile_name' => "CONCAT(first_name, ' ', last_name)"]
                );
                $this->reviewsCollection = $result;
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Review\Block\Product\View\ListView $subject
     * @param string $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\Review\Block\Product\View\ListView $subject,
        $result
    )
    {
        if ($this->userProfileHelper->isEnabled() && $this->userProfileHelper->isReviewsProfileEnabled()) {
            return 'WeltPixel_UserProfile::review/product/view/list.phtml';
        }

        return $result;
    }
}
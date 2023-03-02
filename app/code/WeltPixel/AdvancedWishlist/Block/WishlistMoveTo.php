<?php

namespace WeltPixel\AdvancedWishlist\Block;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Data\Helper\PostHelper;

class WishlistMoveTo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \WeltPixel\AdvancedWishlist\Helper\Data
     */
    protected $_helper;

    /**
     * @var PostHelper
     */
    protected $_postDataHelper;

    /**
     * @param \WeltPixel\AdvancedWishlist\Helper\Data $helper
     * @param PostHelper $postDataHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\WeltPixel\AdvancedWishlist\Helper\Data $helper,
                                PostHelper $postDataHelper,
                                \Magento\Framework\View\Element\Template\Context $context,
                                array $data = [])
    {
        $this->_helper = $helper;
        $this->_postDataHelper = $postDataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isMultiWishlistEnabled()
    {
        return $this->_helper->isMultiWishlistEnabled();
    }

    /**
     * @return bool
     */
    public function isAjaxWishlistEnabled()
    {
        return $this->_helper->isAjaxWishlistEnabled();
    }

    /**
     * @return bool
     */
    public function isShareWishlistEnabled() {
        return $this->_helper->isShareWishlistEnabled();
    }

    /**
     * @return bool
     */
    public function isPriceAlertEnabled() {
        return $this->_helper->isPriceAlertEnabled();
    }

    /**
     * @return bool
     */
    public function isPublicWishlistEnabled() {
        return $this->_helper->isPublicWishlistEnabled();
    }

    /**
     * @return integer
     */
    public function getCurrentWishlistId()
    {
        return (int)$this->getRequest()->getParam('wishlist_id');
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @return string
     */
    public function getMoveToWIshlistParams($item)
    {
        $url = $this->getUrl('wp_collection/multiwishlist/move');
        $params = ['item_id' => $item->getWishlistItemId()];
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] = '';

        return $this->_postDataHelper->getPostData($url, $params);
    }
}

<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_RecentlyViewedBar
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */

namespace WeltPixel\RecentlyViewedBar\Block;

class RecentProducts extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{

    const COLLECTION_TYPE = 'recently_viewed';
    /**
     * @var \WeltPixel\RecentlyViewedBar\Helper\Data
     */
    protected $_avrHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var \Magento\Reports\Block\Product\Widget\Viewed
     */
    protected $_viewProductsBlock;
    /**
     * @var RecentlyViewed
     */
    protected $recentlyViewed;


    /**
     * RecentProducts constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \WeltPixel\RecentlyViewedBar\Helper\Data $avrHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory
     * @param \Magento\Reports\Block\Product\Widget\Viewed $viewedProductsBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \WeltPixel\RecentlyViewedBar\Helper\Data $avrHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
        \Magento\Reports\Block\Product\Widget\Viewed $viewedProductsBlock,
        array $data = []
    )
    {
        $this->_avrHelper                 = $avrHelper;
        $this->_productCollectionFactory  = $productsCollectionFactory;
        $this->_viewProductsBlock         = $viewedProductsBlock;

        $this->setTemplate('recent/products.phtml');

        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }


    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $productCollection =  $this->_getRecentlyViewedCollection();
        return $productCollection;
    }

    /**
     * @return array
     */
    protected function _getRecentlyViewedCollection()
    {
        $limit  = $this->_getProductLimit();
        $random = false;
        if($limit == 0) {
            return [];
        };

        $_collection = $this->_viewProductsBlock->getItemsCollection();
        if ($random) {
            $allIds = $_collection->getAllIds();
            $candidateIds = $_collection->getAllIds();
            $randomIds = [];
            $maxKey = count($candidateIds) - 1;
            while (count($randomIds) <= count($allIds) - 1) {
                $randomKey = rand(0, $maxKey);
                $randomIds[$randomKey] = $candidateIds[$randomKey];
            }

            $_collection->addIdFilter($randomIds);
        };
        if ($limit && $limit > 0 ) {
            $_collection->setPageSize($limit);
        };

        return $_collection;
    }


    /**
     * @return mixed
     */
    protected function _getProductLimit()
    {
        return $this->_avrHelper->getItemLimit();
    }

    /**
     * Retrieve the current store id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return array
     */
    public function getConfigData()
    {
        $isEnabled = $this->_avrHelper->isEnabled();
        $showPrice = $this->_avrHelper->getIsPriceEnabled();
        $showName = $this->_avrHelper->getIsNameEnabled();
        $showImage = $this->_avrHelper->getIsImageEnabled();
        $showAddToCart = $this->_avrHelper->getIsAddtocartEnabled();
        $showAddToCompare = $this->_avrHelper->getIsAddtocompareEnabled();
        $showAddToWishlist = $this->_avrHelper->getIsAddtowishlistEnabled();

        $configData = [
            'is_enabled' => $isEnabled,
            'show_price' => $showPrice,
            'show_name' => $showName,
            'show_image' => $showImage,
            'show_addtocart' => $showAddToCart,
            'show_addtocompare' => $showAddToCompare,
            'show_addtowishlist' => $showAddToWishlist
        ];

        return $configData;
    }


}

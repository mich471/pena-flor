<?php

namespace WeltPixel\AdvancedWishlist\Block\Email;

use Magento\Catalog\Model\Product;
use Magento\Wishlist\Model\Wishlist;

class Price extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var string
     */
    protected $_template = 'WeltPixel_AdvancedWishlist::email/price.phtml';

    /**
     * Product collection array
     *
     * @var Product[]
     */
    protected $products = [];

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * Reset product collection
     *
     * @return void
     */
    public function reset()
    {
        $this->products = [];
        $this->wishlist = null;
    }

    /**
     * Add product to collection
     *
     * @param Product $product
     * @return void
     */
    public function addProduct($product)
    {
        $this->products[$product->getId()] = $product;
    }

    /**
     * Retrieve product collection array
     *
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return Wishlist
     */
    public function getWishlist() {
        return $this->wishlist;
    }

    /**
     * @param Wishlist $wishlist
     */
    public function setWishlist($wishlist) {
        $this->wishlist = $wishlist;
    }
}

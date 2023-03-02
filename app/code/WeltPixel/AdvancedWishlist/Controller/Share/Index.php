<?php
namespace WeltPixel\AdvancedWishlist\Controller\Share;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Wishlist\Model\WishlistFactory;
use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;


class Index extends Action
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * Index constructor.
     * @param PageFactory $pageFactory
     * @param WishlistFactory $wishlistFactory
     * @param WishlistHelper $wishlistHelper
     * @param Context $context
     */
    public function __construct(
        PageFactory $pageFactory,
        WishlistFactory $wishlistFactory,
        WishlistHelper $wishlistHelper,
        Context $context
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $shareCode = $this->getRequest()->getParam('sharecode');
        $isShareEnabled = $this->wishlistHelper->isShareWishlistEnabled();
        if (!$shareCode || !$isShareEnabled) {
            return $this->_redirectBack();
        }

        $wishlistModel = $this->wishlistFactory->create();
        $wishlistModel->load($shareCode, 'sharing_code');

        $resultPage = $this->pageFactory->create();
        $wishlistProductBlock = $resultPage->getLayout()->getBlock('weltpixel.wishlist.product.list');
        if ($wishlistProductBlock) {
            $wishlistProductBlock->assignWishlistModel($wishlistModel);
            if (!$wishlistModel->getId() || $wishlistModel->getDisableShare()) {
                $resultPage->getConfig()->getTitle()->set(__('Wishlist Not Available'));
            } else {
                $pageTitle = __('Wishlist Share');
                if ($this->wishlistHelper->isMultiWishlistEnabled()) {
                    $pageTitle = $wishlistModel->getWishlistName() . ' ' . __('Wishlist Share');
                }
                $resultPage->getConfig()->getTitle()->set($pageTitle);
            }
        }

        return $resultPage;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _redirectBack() {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererOrBaseUrl();
        return $resultRedirect;
    }
}

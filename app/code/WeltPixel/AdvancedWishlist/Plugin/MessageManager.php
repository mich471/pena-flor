<?php

namespace WeltPixel\AdvancedWishlist\Plugin;

use WeltPixel\AdvancedWishlist\Helper\Data as WishlistHelper;

class MessageManager
{

    /**
     * @var WishlistHelper
     */
    protected $_helper;

    /**
     * CustomerSharingBlock constructor.
     * @param WishlistHelper $helper
     */
    public function __construct(
        WishlistHelper $helper
    )
    {
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Message\ManagerInterface $subject
     * @param string $identifier
     * @param array $data
     * @param string|null $group
     */
    public function beforeAddComplexSuccessMessage(
        \Magento\Framework\Message\ManagerInterface $subject,
        string $identifier,
        array $data = [],
        $group = null
    )
    {
        if ($this->_helper->isAjaxWishlistEnabled()) {
            if ($identifier == 'addProductSuccessMessage') {
                $identifier = 'addProductSuccessAjaxMessage';
            }
        }
        return [$identifier, $data, $group];
    }
}

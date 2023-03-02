<?php

namespace WeltPixel\UserProfile\Controller\Account;

use WeltPixel\UserProfile\Controller\Account as AccountAction;

/**
 * Class Index
 * @package WeltPixel\UserProfile\Controller\Index
 */
class Index extends AccountAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_initModuleConfiguration();

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Edit Profile'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}

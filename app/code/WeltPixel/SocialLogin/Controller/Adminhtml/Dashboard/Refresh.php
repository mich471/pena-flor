<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */


namespace WeltPixel\SocialLogin\Controller\Adminhtml\Dashboard;

/**
 * Class Analytics
 * @package WeltPixel\SocialLogin\Controller\Adminhtml\Socialaccounts
 */
class Refresh extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \WeltPixel\Backend\Model\License
     */
    protected $analyticsModel;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WeltPixel\SocialLogin\Model\Analytics $analyticsModel
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->analyticsModel = $analyticsModel;
    }

    public function execute()
    {
        $this->analyticsModel->setAnalyticsData();
        $this->_session->setDebuggerRewite(false);
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/analytics');
    }
}

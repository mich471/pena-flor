<?php
namespace WeltPixel\SmartProductTabs\Controller\Adminhtml\SmartProductTabs;

class Edit extends \WeltPixel\SmartProductTabs\Controller\Adminhtml\SmartProductTabs
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('WeltPixel\SmartProductTabs\Model\SmartProductTabs');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This smart product tab no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('weltpixel_smartproducttab', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Smart Product Tab') : __('New Smart Product Tab'),
            $id ? __('Edit Smart Product Tab') : __('New Smart Product Tab')
        );


        $resultPage->getConfig()->getTitle()->prepend(__('Smart Product Tabs'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getUrl() : __('New Smart Product Tab'));
        return $resultPage;
    }
}

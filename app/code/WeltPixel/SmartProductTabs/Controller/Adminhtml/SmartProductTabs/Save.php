<?php
namespace WeltPixel\SmartProductTabs\Controller\Adminhtml\SmartProductTabs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package WeltPixel\SmartProductTabs\Controller\Adminhtml\SmartProductTabs
 */
class Save extends \WeltPixel\SmartProductTabs\Controller\Adminhtml\SmartProductTabs
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');

            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var \WeltPixel\SmartProductTabs\Model\SmartProductTabs $model */
            $model = $this->_objectManager->create('WeltPixel\SmartProductTabs\Model\SmartProductTabs')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This smart product tab no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            $model->setData($data);
            $model->loadPost($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the smart product tab.'));
                $this->dataPersistor->clear('weltpixel_smartproducttab');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the smart product tab.') . $e->getMessage());
            }

            $this->dataPersistor->set('weltpixel_smartproducttab', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

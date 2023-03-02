<?php

namespace Lc\Core\Controller\Adminhtml\Entity\Action;

use Lc\Core\Controller\Adminhtml\Entity\Action;
use Magento\Eav\Model\Entity;
use Magento\Framework\View\Result\Page;

/**
 * Class Edit
 */
class Edit extends Action
{

    /**
     * @return Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        /** @var $entity Entity */
        $entity = $this->entityFactory->create();
        if ($id) {
            $entity = $entity->load($id);
            $title = $entity->getName();

            if (!$entity->getEntityId()) {
                $this->messageManager->addErrorMessage($this->errorEntityMessage);
                $this->_redirect($this->getPathListAction());
                return;
            }
        }

        $this->coreRegistry->register('row_data', $entity);

        $resultPage = $this->resultPageFactory->create();

        $title = $id ? __('Edit') . ' ' . $title : __('New');

        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}
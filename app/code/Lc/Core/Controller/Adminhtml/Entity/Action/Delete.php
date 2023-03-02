<?php

namespace Lc\Core\Controller\Adminhtml\Entity\Action;

use Lc\Core\Controller\Adminhtml\Entity\Action;
use Magento\Eav\Model\Entity;

/**
 * Class Delete
 */
class Delete extends Action
{

    /**
     * Add New Entity.
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();

        if ($id) {
            /** @var $entity Entity */
            $entity = $entity->load($id);

            if (!$entity->getEntityId()) {
                $this->messageManager->addErrorMessage($this->errorEntityMessage);
                $this->_redirect($this->getPathListAction());
                return;
            } else {
                if ($entity->delete()) {
                    $this->messageManager->addSuccessMessage($this->successDeleteEntityMessage);
                    $this->_redirect($this->getPathListAction());
                    return;
                } else {
                    $this->messageManager->addErrorMessage($this->errorDeleteEntityMessage);
                    $this->_redirect($this->getPathListAction());
                    return;
                }
            }
        }
    }

}
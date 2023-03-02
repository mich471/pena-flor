<?php

namespace Lc\Core\Controller\Adminhtml\Entity\Action;

use Lc\Core\Controller\Adminhtml\Entity\Action;
use Magento\Eav\Model\Entity;

/**
 * Class Save
 */
class Save extends Action
{

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect($this->getPathListAction());
            return;
        }

        try {
            /** @var $entity Entity */
            $entity = $this->entityFactory->create();

            $entity->setData($this->processEntityData($data));
            if ($id) {
                $entity->setEntityId($id);
            }

            $entity->save();

            $this->messageManager->addSuccessMessage($this->successEntityMessage);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_redirect($this->getPathListAction());
    }


}
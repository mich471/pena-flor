<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Field;

use Amasty\Feed\Controller\Adminhtml\AbstractField;

class Delete extends AbstractField
{
    /**
     * @var \Amasty\Feed\Api\CustomFieldsRepositoryInterface
     */
    private $repository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Feed\Api\CustomFieldsRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct($context);
    }

    public function execute()
    {
        if ($fieldId = $this->getRequest()->getParam('id')) {
            try {
                $this->repository->deleteAllConditions($fieldId, true);

                $this->messageManager->addSuccessMessage(__('You deleted the field.'));

                return $this->resultRedirectFactory->create()->setPath('amfeed/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete the field right now. Please review the log and try again.')
                );

                return $this->resultRedirectFactory->create()->setPath(
                    'amfeed/*/edit',
                    ['id' => $this->getRequest()->getParam('id')]
                );
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a field to delete.'));

        return $this->resultRedirectFactory->create()->setPath('amfeed/*/');
    }
}

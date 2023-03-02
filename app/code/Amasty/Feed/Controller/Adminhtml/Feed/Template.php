<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Feed;

class Template extends AbstractMassAction
{
    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     */
    public function massAction($collection)
    {
        foreach ($collection as $model) {
            $this->feedCopier->template($model);
            $this->messageManager->addSuccessMessage(__('Template %1 was created', $model->getName()));
        }
    }
}

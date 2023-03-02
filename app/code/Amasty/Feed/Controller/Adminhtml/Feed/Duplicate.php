<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\Feed;

class Duplicate extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    public function massAction($collection)
    {
        foreach ($collection as $model) {
            $this->feedCopier->copy($model);
            $this->messageManager->addSuccessMessage(__('Feed %1 was duplicated', $model->getName()));
        }
    }
}

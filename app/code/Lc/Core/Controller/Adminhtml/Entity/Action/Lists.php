<?php

namespace Lc\Core\Controller\Adminhtml\Entity\Action;

use Lc\Core\Controller\Adminhtml\Entity\Action;

/**
 * Class Lists
 *
 */
class Lists extends Action
{

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}
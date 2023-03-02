<?php

namespace Lc\Core\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action as CoreAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

/**
 * Class Zone
 */
abstract class Action extends CoreAction
{

    /**
     * @var Context $context
     */
    protected $context;

    /**
     * @var Registry $coreRegistry
     */
    protected $coreRegistry;

    /**
     * @var object
     */
    protected $entityFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $successEntityMessage;

    /**
     * @var string
     */
    protected $errorEntityMessage;
    /**
     * @var string
     */
    protected $successDeleteEntityMessage;

    /**
     * @var string
     */
    protected $errorDeleteEntityMessage;

    /**
     * @var string
     */
    protected $basePathActions;

    /**
     * For implementations redefine this constructor to set
     * dependency injection in the parameter $entityFactory
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        LoggerInterface $logger,
        $entityFactory
    )
    {
        parent::__construct($context);
        $this->context = $context;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->successEntityMessage = __('The entity has successful create.');
        $this->errorEntityMessage = __('Entity not exists.');
        $this->successDeleteEntityMessage = __('Entity was deleted successfully.');
        $this->errorDeleteEntityMessage = __('Entity not deleted.');
        $this->entityFactory = $entityFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getPathListAction()
    {
        return $this->getBasePathActions() . "/lists";
    }

    /**
     * @return string
     */
    protected function getPathDeleteAction()
    {
        return $this->getBasePathActions() . "/delete";
    }

    /**
     * @return string
     */
    protected function getPathEditAction()
    {
        return $this->getBasePathActions() . "/edit";
    }

    /**
     * @return string
     */
    protected function getPathSaveAction()
    {
        return $this->getBasePathActions() . "/save";
    }

    /**
     * @return string
     */
    protected function getBasePathActions()
    {
        return $this->basePathActions;
    }

    /**
     * @param $basePathActions
     * @return $this
     */
    protected function setBasePathActions($basePathActions)
    {
        $this->basePathActions = $basePathActions;
        return $this;
    }

    /**
     * @return object
     */
    protected function getEntityFactory()
    {
        return $this->entityFactory;
    }

    /**
     * @param $entityFactory
     * @return $this
     */
    protected function setEntityFactory($entityFactory)
    {
        $this->entityFactory = $entityFactory;
        return $this;
    }

    /**
     * @param array $data
     * @return array $data
     */
    protected function processEntityData($data)
    {
        return $data;
    }

}
<?php
namespace TAC\RedirectFamilia\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\TestFramework\Event\Magento;
use Psr\Log\LoggerInterface;

/**
 * Class CheckAgeObserver
 *
 * @package LC\AgeBlock\Observer
 */
class NewHomeByGroup
    implements ObserverInterface
{
    /** Store Code for this observer */
    CONST ALLOWED_STORE_CODE = 'tac';

    /** Familia URL KEY FOR REDIRECT */
    CONST FAMILIA_URL_KEY = 'familia';

    /** User group that need redirect */
    protected $redirectGroups = [6, 7, 10];

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /** Customer Session */
    protected $_customerSession;


    public function __construct(
        RedirectInterface $redirect,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        Session $session
    ) {
        $this->redirect = $redirect;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->_customerSession = $session;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeCode = $this->_storeManager->getStore()->getCode();
        //this script will run only in determinated stores
        if ($storeCode != self::ALLOWED_STORE_CODE) {
            return $this;
        }
        $user = $this->_customerSession->getCustomer();
        if (!$user->getId()) {
            return $this;
        }

        /** @var $request \Magento\Framework\App\Request\Http */
        $request = $observer->getEvent()->getRequest();

        $actionName = $request->getControllerName();
        $controller = $observer->getControllerAction();

        if (
            $actionName == 'index'
            && in_array($user->getGroupId(), $this->redirectGroups)
            && ( ($request->getPathInfo() == '/' ) || ($request->getPathInfo() == '') )
        ) {
            $this->redirect->redirect($controller->getResponse(), self::FAMILIA_URL_KEY);
        }
        return $this;
    }
}
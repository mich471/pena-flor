<?php

namespace Vi\CustomerAttributeValidator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SaveNombreEmpresaAttribute
 * @package Vi\CustomerAttributeValidator\Observer
 */
class SaveNombreEmpresaAttribute implements ObserverInterface
{
    const NOMBRE_EMPRESA_ATTRIBUTE = 'nombre_empresa';

    const VI_STORE_CODE = 'ventainstitucional';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * SaveNombreEmpresaAttribute constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request, StoreManagerInterface $storeManager)
    {
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $storeCode = $this->storeManager->getStore()->getCode();
        } catch (NoSuchEntityException $e) {
            return;
        }
        if ($storeCode !== self::VI_STORE_CODE) {
            return;
        }

        /** @var Customer $customer */
        $customer = $observer->getCustomer();

        if (!$customer->getData(self::NOMBRE_EMPRESA_ATTRIBUTE)) {
            $customer->setData(
                self::NOMBRE_EMPRESA_ATTRIBUTE,
                $this->request->getParam(self::NOMBRE_EMPRESA_ATTRIBUTE)
            );
        }
    }
}
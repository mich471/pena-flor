<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const METHOD_LABEL_PATH = 'carriers/matrixrate/shipping_method_labels';
    const IS_CONDITION_FROM_INCLUSIVE = 'carriers/matrixrate/condition_from_inclusive';

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;
    /**
     * @var Json
     */
    protected Json $json;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        Json                  $json
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->json = $json;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function getIsConditionFromInclusive()
    {
        return (bool)$this->getStoreConfig(
            self::IS_CONDITION_FROM_INCLUSIVE
        );
    }

    /**
     * @param $method
     * @return mixed|string
     * @throws NoSuchEntityException
     */
    public function getLabel($method)
    {
        $labels = $this->getMethodLabel();

        if ($labels) {
            foreach ($labels as $label) {
                $configMethod = trim(strtolower($label['shipping_method']));
                $toTestMethod = trim(strtolower($method));
                if ($configMethod == $toTestMethod) {
                    return $label['label'];
                }
            }
        }

        return '';
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     * @throws NoSuchEntityException
     */
    public function getMethodLabel()
    {
        $config =  $this->getStoreConfig(
            self::METHOD_LABEL_PATH
        );

        if (is_array($config)) {
            return $config;
        }

        if ($config) {
            return $this->json->unserialize($config);
        }

        return false;
    }

    /**
     * @param $path
     * @param $store
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getStoreConfig($path, $store = null)
    {
        $store = $store ?: $this->storeManager->getStore()->getId();

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORES,
            $store
        );
    }
}

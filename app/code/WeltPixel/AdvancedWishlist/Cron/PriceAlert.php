<?php

namespace WeltPixel\AdvancedWishlist\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\AdvancedWishlist\Model\ResourceModel\PriceAlert\CollectionFactory as PriceAlertCollectionFactory;
use WeltPixel\AdvancedWishlist\Model\Email;
use Magento\Store\Model\App\Emulation;

class PriceAlert {

    /**
     * Allow price alert
     *
     */
    const XML_PATH_PRICE_ALLOW = 'weltpixel_advancedwishlist/general/pricealert_frequency';

    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Website collection array
     *
     * @var array
     */
    protected $websites;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PriceAlertCollectionFactory
     */
    protected $priceAlertCollectionFactory;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * PriceAlert constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param ProductRepositoryInterface $productRepository
     * @param PriceAlertCollectionFactory $priceAlertCollectionFactory
     * @param Email $email
     * @param Emulation $appEmulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        PriceAlertCollectionFactory $priceAlertCollectionFactory,
        Email $email,
        Emulation $appEmulation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->priceAlertCollectionFactory = $priceAlertCollectionFactory;
        $this->email = $email;
        $this->appEmulation = $appEmulation;
    }

    /**
     * Retrieve website collection array
     *
     * @return array
     * @throws \Exception
     */
    protected function _getWebsites()
    {
        if ($this->websites === null) {
            try {
                $this->websites = $this->storeManager->getWebsites();
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
                throw $e;
            }
        }
        return $this->websites;
    }


    /**
     * @inheritdoc
     */
    public function execute()
    {
        foreach ($this->_getWebsites() as $website) {
            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!$this->scopeConfig->getValue(
                self::XML_PATH_PRICE_ALLOW,
                ScopeInterface::SCOPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )
            ) {
                continue;
            }

            try {
                $collection = $this->priceAlertCollectionFactory->create()->addWebsiteFilter(
                    $website->getId()
                )->setCustomerAndWishlistOrder();
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
                throw $e;
            }

            $previousCustomer = null;
            $this->email->setWebsite($website);
            foreach ($collection as $priceAlert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $priceAlert->getCustomerId()) {
                        $customer = $this->customerRepository->getById($priceAlert->getCustomerId());
                        if ($previousCustomer) {
                            $this->email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $this->email->clean();
                        $this->email->setCustomerData($customer);
                    } else {
                        $customer = $previousCustomer;
                    }

                    $storeId = $website->getDefaultStore()->getId();
                    if ($customer->getStoreId() > 0 ) {
                        $storeId = $customer->getStoreId();
                    }
                    $this->appEmulation->startEnvironmentEmulation($storeId);

                    $product = $this->productRepository->getById(
                        $priceAlert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    $product->setCustomerGroupId($customer->getGroupId());
                    $productPrice = $product->getFinalPrice();

                    if (in_array($product->getTypeId(), ['bundle', 'grouped'])) {
                        $priceInfo = $product->getPriceInfo()->getPrice('final_price');
                        $productPrice = $priceInfo->getMinimalPrice()->getValue();
                    }

                    $this->appEmulation->stopEnvironmentEmulation();

                    if ($priceAlert->getPrice() != $productPrice) {
                        if ($priceAlert->getPrice() > $productPrice) {
                            $product->setPriceAlertOldPrice($priceAlert->getPrice());
                            $product->setPriceAlertNewPrice($productPrice);
                            $this->email->addProduct($product, $priceAlert->getWishlistId());
                        }
                        $priceAlert->setPrice($productPrice);
                        $priceAlert->save();
                    }
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $this->email->send();
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }
    }
}
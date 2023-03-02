<?php

namespace WeltPixel\AdvancedWishlist\Model;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Area;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\Context;use Magento\Framework\Registry;use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\App\Emulation;
use Magento\Wishlist\Model\WishlistFactory;
use WeltPixel\AdvancedWishlist\Block\Email\Price;

/**
 * Class Email
 * @package WeltPixel\AdvancedWishlist\Model
 */
class Email extends AbstractModel
{
    /**
     * Website Model
     *
     * @var Website
     */
    protected $website;

    /**
     * Customer model
     *
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * Products collection where changed price
     *
     * @var array
     */
    protected $products = [];

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;


    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var View
     */
    protected $customerHelper;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'weltpixel_wishlist_productalert_email_price_template';

    /**
     * Email constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param View $customerHelper
     * @param TransportBuilder $transportBuilder
     * @param Emulation $appEmulation
     * @param LayoutInterface $layout
     * @param WishlistFactory $wishlistFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        View $customerHelper,
        TransportBuilder $transportBuilder,
        Emulation $appEmulation,
        LayoutInterface $layout,
        WishlistFactory $wishlistFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->transportBuilder = $transportBuilder;
        $this->customerHelper = $customerHelper;
        $this->appEmulation = $appEmulation;
        $this->layout = $layout;
        $this->wishlistFactory = $wishlistFactory;
        parent::__construct($context, $registry);
    }


    /**
     * Set website model
     *
     * @param Website $website
     *
     * @return $this
     */
    public function setWebsite(Website $website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function setWebsiteId($websiteId)
    {
        $this->website = $this->storeManager->getWebsite($websiteId);
        return $this;
    }

    /**
     * Set customer by id
     *
     * @param int $customerId
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setCustomerId($customerId)
    {
        $this->customer = $this->customerRepository->getById($customerId);
        return $this;
    }

    /**
     * Set customer model
     *
     * @param CustomerInterface $customer
     *
     * @return $this
     */
    public function setCustomerData($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Clean data
     *
     * @return $this
     */
    public function clean()
    {
        $this->customer = null;
        $this->products = [];

        return $this;
    }

    /**
     * Add product (price change) to collection
     *
     * @param Product $product
     * @param int $wishlistId
     *
     * @return $this
     */
    public function addProduct($product, $wishlistId)
    {
        $this->products[$wishlistId][$product->getId()] = $product;///$product->getName();
        return $this;
    }

    /**
     * Send customer email
     *
     * @return bool
     * @throws MailException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function send()
    {
        if ($this->website === null || $this->customer === null) {
            return false;
        }

        if (!$this->website->getDefaultGroup() || !$this->website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        if (count($this->products) === 0) {
            return false;
        }

        $storeId = $this->website->getDefaultStore()->getId();
        if ($this->customer->getStoreId() > 0 ) {
            $storeId = $this->customer->getStoreId();
        }

        $alertContent = '';

        $this->appEmulation->startEnvironmentEmulation($storeId);
        foreach ($this->products as $wishlistId =>  $products) {
            $block = $this->layout->createBlock(Price::class);
            $wishlist = $this->wishlistFactory->create()->load($wishlistId);
            $block->reset();
            $block->setWishlist($wishlist);
            foreach ($products as $product) {
                $product->setCustomerGroupId($this->customer->getGroupId());
                $block->addProduct($product);
            }
            $alertContent .= $this->_appState->emulateAreaCode(
                Area::AREA_FRONTEND,
                [$block, 'toHtml']
            );
        }
        $this->appEmulation->stopEnvironmentEmulation();

        $customerName = $this->customerHelper->getCustomerName($this->customer);
        $templateId = self::XML_PATH_EMAIL_TEMPLATE;

        $this->transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'customerName' => $customerName,
                'alertContent' => $alertContent,
            ]
        )->setFrom(
            $this->scopeConfig->getValue(
                self::XML_PATH_EMAIL_SENDER,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->addTo(
            $this->customer->getEmail(),
            $customerName
        )->getTransport()->sendMessage();

        return true;
    }
}

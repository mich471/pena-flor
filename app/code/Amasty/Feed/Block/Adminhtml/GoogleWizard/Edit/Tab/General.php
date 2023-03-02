<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Model\OptionSource\Feed\StoreOption;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class General extends TabGeneric
{
    public const HTML_ID_PREFIX = 'feed_googlewizard_general_';

    /**
     * @var \Amasty\Feed\Model\GoogleWizard
     */
    private $googleWizard;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var \Amasty\Feed\Model\FeedRepository
     */
    private $feedRepository;

    /**
     * @var StoreOption
     */
    private $storeOption;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        \Amasty\Feed\Model\GoogleWizard $googleWizard,
        \Magento\Store\Model\System\Store $systemStore = null, // @deprecated. Backward compatibility
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Amasty\Feed\Model\FeedRepository $feedRepository,
        StoreOption $storeOption = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $registryContainer, $data);
        $this->googleWizard = $googleWizard;
        $this->currencyFactory = $currencyFactory;
        $this->feedRepository = $feedRepository;
        $this->storeOption = $storeOption ?? ObjectManager::getInstance()->get(StoreOption::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Step 1: General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Step 1: General Settings');
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareNotEmptyForm()
    {
        /** @var \Amasty\Feed\Model\Feed $model */
        if ($feedId = $this->_request->getParam('amfeed_id')) {
            try {
                $model = $this->feedRepository->getById($feedId);
            } catch (NoSuchEntityException $exception) {
                $model = $this->feedRepository->getEmptyModel();
            }
        } else {
            $model = $this->feedRepository->getEmptyModel();
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix(self::HTML_ID_PREFIX);
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('general_fieldset', ['legend' => $this->getLegend()]);

        if ($model->getId()) {
            $fieldset->addField(FeedInterface::ENTITY_ID, 'hidden', ['name' => 'feed_entity_id']);
        } else {
            $model->setData(FeedInterface::IS_ACTIVE, 1);

            $model->setData(FeedInterface::CSV_COLUMN_NAME, 1);

            $model->setData(FeedInterface::FORMAT_PRICE_CURRENCY_SHOW, 1);
            $model->setData(FeedInterface::FORMAT_PRICE_DECIMALS, 'two');
            $model->setData(FeedInterface::FORMAT_PRICE_DECIMAL_POINT, 'dot');
            $model->setData(FeedInterface::FORMAT_PRICE_THOUSANDS_SEPARATOR, 'comma');

            $model->setData(FeedInterface::FORMAT_DATE, 'Y-m-d');
        }

        $fieldset->addField(
            FeedInterface::NAME,
            'text',
            [
                'name' => FeedInterface::NAME,
                'label' => __('Feed Name'),
                'title' => __('Feed Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            FeedInterface::FILENAME,
            'text',
            [
                'name' => FeedInterface::FILENAME,
                'label' => __('File Name'),
                'title' => __('File Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            FeedInterface::IS_ACTIVE,
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => FeedInterface::IS_ACTIVE,
                'required' => true,
                'options' => [
                    '1' => __('Active'),
                    '0' => __('Inactive')
                ]
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                FeedInterface::STORE_ID,
                'select',
                [
                    'label' => __('Store View'),
                    'class' => 'required-entry',
                    'required' => true,
                    'name' => FeedInterface::STORE_ID,
                    'value' => $this->googleWizard->getStoreId(),
                    'values' => $this->storeOption->toOptionArray()
                ]
            );
        } else {
            $fieldset->addField(
                FeedInterface::STORE_ID,
                'hidden',
                [
                    'value' => $this->googleWizard->getStoreId()
                ]
            );
        }

        $fieldset->addField(
            FeedInterface::FORMAT_PRICE_CURRENCY,
            'select',
            [
                'label' => __('Price Currency'),
                'name'  => FeedInterface::FORMAT_PRICE_CURRENCY,
                'value' => $this->googleWizard->getCurrency(),
                'options' => $this->getCurrencyList(),
            ]
        );

        $fieldset->addField(
            FeedInterface::EXCLUDE_DISABLED,
            'select',
            [
                'label' => __('Exclude Disabled Products'),
                'title' => __('Exclude Disabled Products'),
                'name' => FeedInterface::EXCLUDE_DISABLED,
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );

        $fieldset->addField(
            FeedInterface::EXCLUDE_SUBDISABLED,
            'select',
            [
                'label' => __('Exclude Child Products if Parent Product Is Disabled'),
                'title' => __('Exclude Child Products if Parent Product Is Disabled'),
                'name' => FeedInterface::EXCLUDE_SUBDISABLED,
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );

        $fieldset->addField(
            FeedInterface::EXCLUDE_OUT_OF_STOCK,
            'select',
            [
                'label' => __('Exclude Out of Stock Products'),
                'title' => __('Exclude Out of Stock Products'),
                'name' => FeedInterface::EXCLUDE_OUT_OF_STOCK,
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );

        $fieldset->addField(
            FeedInterface::EXCLUDE_NOT_VISIBLE,
            'select',
            [
                'label' => __('Exclude Not Visible Products'),
                'title' => __('Exclude Not Visible Products'),
                'name' => FeedInterface::EXCLUDE_NOT_VISIBLE,
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );

        $dependencies = $this->getLayout()->createBlock(Dependence::class)
            ->addFieldMap($htmlIdPrefix . FeedInterface::EXCLUDE_DISABLED, FeedInterface::EXCLUDE_DISABLED)
            ->addFieldMap($htmlIdPrefix . FeedInterface::EXCLUDE_SUBDISABLED, FeedInterface::EXCLUDE_SUBDISABLED)
            ->addFieldDependence(FeedInterface::EXCLUDE_SUBDISABLED, FeedInterface::EXCLUDE_DISABLED, 1);

        $form->setValues($model->getData());
        $this->setForm($form);
        $this->setChild('form_after', $dependencies);

        return $this;
    }

    /**
     * Get currencies
     *
     * @return array
     */
    protected function getCurrencyList()
    {
        $instantCurrencyFactory = $this->currencyFactory->create();
        $currencies = $instantCurrencyFactory->getConfigAllowCurrencies();

        rsort($currencies);
        $retCurrencies = array_combine($currencies, $currencies);

        return $retCurrencies;
    }
}

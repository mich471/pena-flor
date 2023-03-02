<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\Feed\Edit\Tab;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Model\OptionSource\Feed\StoreOption;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\App\ObjectManager;

class General extends Generic implements TabInterface
{
    /**
     * @var \Amasty\Feed\Model\Config\Source\Compress
     */
    private $compressSource;

    /**
     * @var StoreOption
     */
    private $storeOption;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Feed\Model\Config\Source\Compress $compressSource,
        StoreOption $storeOption = null,
        array $data = []
    ) {
        $this->compressSource = $compressSource;
        $this->storeOption = $storeOption ?? ObjectManager::getInstance()->get(StoreOption::class);
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     * @codeCoverageIgnore
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Form
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amfeed_feed');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('feed_');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

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
                'label' => __('Name'),
                'title' => __('Name'),
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

        $typeOptions = [
            'label' => __('Type'),
            'title' => __('Type'),
            'name' => FeedInterface::FEED_TYPE,
            'required' => true,

            'options' => [
                'csv' => __('CSV'),
                'xml' => __('XML'),
                'txt' => 'TXT'
            ]
        ];

        if ($model->getId()) {
            $typeOptions['readonly'] = true;
            $feedType = $model->getFeedType();
            $feedTypeText = $typeOptions['options'][$feedType];
            if ($feedType && $feedTypeText) {
                $typeOptions['options'] = [$feedType => $feedTypeText];
            }
        }

        $fieldset->addField(
            FeedInterface::FEED_TYPE,
            'select',
            $typeOptions
        );

        $fieldset->addField(
            FeedInterface::STORE_ID,
            'select',
            [
                'name' => FeedInterface::STORE_ID,
                'label' => __('Store'),
                'title' => __('Store'),
                'required' => true,
                'options' => $this->storeOption->toOptionArray()
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

        $fieldset->addField(
            FeedInterface::COMPRESS,
            'select',
            [
                'label' => __('Compress'),
                'name' => FeedInterface::COMPRESS,
                'options' => $this->compressSource->toArray()
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

        return parent::_prepareForm();
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\Feed;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Backend\Block\Widget\Context;

class Template extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $feedCollectionFactory,
        array $data = []
    ) {
        $this->feedCollectionFactory = $feedCollectionFactory;
        parent::__construct($context, $data);

        $this->addSetupGoogleFeedButton();
        $this->addNewButton();
        $this->addUnlockButton();
    }

    /**
     * Add setup google wizard button
     *
     * @return $this
     */
    public function addSetupGoogleFeedButton()
    {
        $this->addButton(
            'googleFeed',
            [
                'label'   => __("Setup Google Feed"),
                'class'   => 'google-feed primary',
                'onclick' => 'setLocation(\'' . $this->getCreateGoogleFeedUrl()
                    . '\')'
            ]
        );

        return $this;
    }

    /**
     * Add new feed button
     *
     * @return $this
     */
    public function addNewButton()
    {
        $this->addButton(
            'add',
            [
                'label' => __("Add New Feed"),
                'class' => 'add primary',
                'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
                'options' => $this->getOptions()
            ]
        );

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [
            [
                'label' => __('Custom Feed'),
                'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                'default' => true,
            ]
        ];

        $templates = $this->feedCollectionFactory->create()
            ->addFieldToSelect(FeedInterface::ENTITY_ID)
            ->addFieldToSelect(FeedInterface::NAME)
            ->addFieldToFilter(FeedInterface::IS_TEMPLATE, 1)
            ->getData();

        foreach ($templates as $template) {
            $options[] = [
                'label' => __('Add %1 Template', $template[FeedInterface::NAME]),
                'onclick' => "setLocation('" . $this->getUrl('*/*/fromTemplate', [
                        'id' => $template[FeedInterface::ENTITY_ID]
                ]) . "')",
            ];
        }

        return $options;
    }

    /**
     * Get new url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get google feed url
     *
     * @return string
     */
    public function getCreateGoogleFeedUrl()
    {
        return $this->getUrl('*/googleWizard/index');
    }

    public function addUnlockButton()
    {
        $alertMessage = __('Are you sure you want to do this?');
        $onClick = sprintf('confirmSetLocation("%s", "%s")', $alertMessage, $this->getUnlockUrl());

        $this->addButton(
            'forceUnlock',
            [
                'label' => __("Force Unlock"),
                'class' => 'unlock',
                'onclick' => $onClick,
                'sort_order' => 5
            ]
        );

        return $this;
    }

    private function getUnlockUrl()
    {
        return $this->getUrl('*/feed/unlock');
    }
}

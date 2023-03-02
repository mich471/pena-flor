<?php

namespace WeltPixel\CmsBlockScheduler\Model\ResourceModel\Widget\Instance\Options;

class Tag implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \WeltPixel\CmsBlockScheduler\Model\Tag
     */
    protected $_tagCollectionFactory;

    /**
     * @param \Magento\Theme\Model\ResourceModel\Theme\Collection $widgetResourceModel
     */
    public function __construct(
        \Magento\Theme\Model\ResourceModel\Theme\Collection $widgetResourceModel,
        \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag\CollectionFactory  $tagCollectionFactory
    )
    {
        $this->_resourceModel = $widgetResourceModel;
        $this->_tagCollectionFactory = $tagCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $tagCollection = $this->_tagCollectionFactory->create();

        $options = [];
	    $options[] = [
		    'value' => '',
		    'label' => __(' '),
	    ];
        foreach ($tagCollection as $tag) {
            $options[] = [
                'value' => $tag->getTitle(),
                'label' => $tag->getTitle(),
            ];
        }

        return $options;
    }
}

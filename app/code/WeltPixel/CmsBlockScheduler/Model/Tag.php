<?php

namespace WeltPixel\CmsBlockScheduler\Model;

use Magento\Customer\Model\GroupFactory;

/**
 * Tag Model
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Tag extends \Magento\Framework\Model\AbstractModel
{

    /**
     * store view id.
     *
     * @var int
     */
    protected $_storeViewId = null;

    /**
     * [$_formFieldHtmlIdPrefix description].
     *
     * @var string
     */
    protected $_formFieldHtmlIdPrefix = 'page_';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_groupCollectionFactory;
    protected $_tagsCollectionFactory;

    /**
     * logger.
     *
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_monolog;

    protected $_tagFactory;
    protected $_valueFactory;
    protected $_valueCollectionFactory;

    /**
     * [__construct description].
     *
     * @param \Magento\Framework\Model\Context                                          $context
     * @param \Magento\Framework\Registry                                               $registry
     * @param \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag                   $resource
     * @param \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag\Collection        $resourceCollection
     * @param \WeltPixel\CmsBlockScheduler\Model\TagFactory                          $tagFactory
     * @param \Magento\Store\Model\StoreManagerInterface                                $storeManager
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag $resource,
        \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag\Collection $resourceCollection,
        \WeltPixel\CmsBlockScheduler\Model\TagFactory $tagFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Logger\Monolog $monolog,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \WeltPixel\CmsBlockScheduler\Model\ResourceModel\Tag\CollectionFactory $tagsCollectionFactory
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
        $this->_tagFactory = $tagFactory;
        $this->_storeManager = $storeManager;

        $this->_monolog = $monolog;

        $this->_groupCollectionFactory = $groupCollectionFactory;
        $this->_tagsCollectionFactory = $tagsCollectionFactory;

        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }
}

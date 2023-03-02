<?php

namespace WeltPixel\CmsBlockScheduler\Model;

/**
 * Tag Model
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Block extends \Magento\Cms\Model\Block
{
    protected $_date;
    protected $_helper;
    protected $_customerSession;
    protected $_httpContext;

    /**
     * @return void
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \WeltPixel\CmsBlockScheduler\Helper\Data  $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_date = $date;
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
        $this->_httpContext = $httpContext;
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->_helper->resourceEnabled('customer_group')) {
            $block_customerGroupId = explode(',', $this->getCustomerGroup());
            $customerGroupId = 0;

            if ($this->_customerSession->isLoggedIn()) {
                $customerGroupId = $this->_customerSession->getCustomerGroupId();
            }
            if ($this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
                $customerGroupId = $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
            }

            $fvalue = array_key_exists(0, $block_customerGroupId) ? $block_customerGroupId[0] : '';
            if ($fvalue != '' && !in_array($customerGroupId, $block_customerGroupId)) {
                return false;
            }
        }

        if ($this->_helper->resourceEnabled('date_range')) {
            $validFrom = $this->getValidFrom();
            $validTo   = $this->getValidTo();

            $now = $this->_date->gmtDate();

            if (($validFrom && $validFrom > 0) && ($validFrom > $now)) {
                return false;
            }
            if (($validTo && $validTo > 0) && ($validTo < $now)) {
                return false;
            }
        }

        return parent::isActive();
    }

    /**
     * Save object data
     *
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        if ($this->getCustomerGroup() && is_array($this->getCustomerGroup())) {
            $customerGroup = implode(',', $this->getCustomerGroup());
            $this->setCustomerGroup($customerGroup);
        }

        if (!$this->getData('ignore_cron_schedule_flag')) {
            $validFrom = $this->getValidFrom();
            $validTo   = $this->getValidTo();
            $now = $this->_date->gmtDate();
            $flagValue = 1;

            $this->convertDate($validFrom);
            $this->convertDate($validTo);

            if (($validFrom && $validFrom > 0) && ($validFrom > $now)) {
                $flagValue  = 0;
            } elseif (($validTo && $validTo > 0) && ($validTo < $now)) {
                $flagValue = 0;
            }

            $this->setData('cron_schedule_flag', $flagValue);
        }

        parent::beforeSave();
    }

    /**
     * @param $dateValue
     * @throws \Exception
     */
    protected function convertDate(&$dateValue)
    {
        $convertedDate = (new \DateTime())->setTimestamp(strtotime($dateValue));
        $dateValue = $convertedDate->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
    }
}

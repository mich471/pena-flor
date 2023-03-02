<?php
namespace WeltPixel\SmartProductTabs\Model\ResourceModel;

/**
 * Class SmartProductTabs
 * @package WeltPixel\SmartProductTabs\Model\ResourceModel
 */
class SmartProductTabs extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_smartproducttabs', 'id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Exception
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = $object->getData('store_id');
        if (is_array($storeId)) {
            $object->setData('store_id', implode(",", $storeId));
        }

        $customerGroup = $object->getData('customer_group');
        if (is_array($customerGroup)) {
            $object->setData('customer_group', implode(",", $customerGroup));
        }

        return $this;
    }
}

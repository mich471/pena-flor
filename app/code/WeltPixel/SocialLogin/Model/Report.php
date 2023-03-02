<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */


namespace WeltPixel\SocialLogin\Model;

/**
 * Class Report
 * @package WeltPixel\SocialLogin\Model
 */
class Report extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var array
     */
    protected $_socialMedia = [
        'fb' => 'Facebook',
        'amazon' => 'Amazon',
        'google' => 'Google',
        'instagram' => 'Instagram',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'paypal' => 'PayPal',
        'default' => 'Email & Password',
        'guest' => 'Guest'
    ];


    const CACHE_TAG = 'weltpixel_sociallogin_report';
    /**
     * @var string
     */
    protected $_cacheTag = 'weltpixel_sociallogin_report';
    /**
     * @var string
     */
    protected $_eventPrefix = 'weltpixel_sociallogin_report';

    protected $_createdDate;

    protected function _construct()
    {
        $this->_init('WeltPixel\SocialLogin\Model\ResourceModel\Report');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $type
     * @param $typeData
     * @return $this
     * @throws \Exception
     */
    public function setReportData($type, $typeData)
    {
        $this->setData([
            'type' => $type,
            'type_data' => $typeData,
        ])
            ->setId(null)
            ->save();

        return $this;
    }

    /**
     * truncate table 'weltpixel_sociallogin_analytics'
     */
    public function truncateWpSlAnalytics() {
        $connection = $this->getResource()->getConnection();
        $tableName = $this->getCollection()->getMainTable();

        $connection->truncateTable($tableName);
    }

    /**
     * @return mixed
     */
    public function getAnalyticsTotals() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $total = '';
        $collection = $this->getCollection()->addFieldToFilter('type', 'total');
        if($collection->getSize() > 0) {
            $serializer = $objectManager->get(\Magento\Framework\Serialize\Serializer\Serialize::class);
            $objectFactory = $objectManager->get(\Magento\Framework\DataObjectFactory::class);
            $serializedTotal = $collection->getFirstItem()->getTypeData();
            $total = $objectFactory->create()->setData($serializer->unserialize($serializedTotal));
        }

        return $total;
    }

    /**
     * @return array
     */
    public function getAnalyticsData() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectFactory = $objectManager->get(\Magento\Framework\DataObjectFactory::class);
        $analyticsDataArr = [];
        $collection = $this->getCollection();
        if($collection->getSize() > 0) {
            $serializer = $objectManager->get(\Magento\Framework\Serialize\Serializer\Serialize::class);
            foreach($collection as $data) {
                if($data->getType() != 'total') {
                    $analyticsDataArr[$this->_socialMedia[$data->getType()]] = $objectFactory->create()->setData($serializer->unserialize($data->getTypeData()));
                }
                if(!$this->_createdDate) {
                    $this->_createdDate = $data->getCreatedAt();
                }
            }
        }

        return $analyticsDataArr;
    }

    /**
     * @return string
     */
    public function getLastUpdate() {
        return $this->_createdDate ?? '';
    }


}

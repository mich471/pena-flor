<?php
namespace SummaSolutions\WebShopAppsMatrixRate\Model;

use WebShopApps\MatrixRate\Model\ResourceModel\Carrier\MatrixRate;
use Magento\Framework\Model\AbstractExtensibleModel;

class Rate
    extends AbstractExtensibleModel
    implements \SummaSolutions\WebShopAppsMatrixRate\Api\Data\RateInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\WebShopApps\MatrixRate\Model\ResourceModel\Carrier\Matrixrate::class);
    }

    /**
     * @return mixed|null
     */
    public function getPk()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @param $pk
     * @return mixed|Rate
     */
    public function setPk($pk)
    {
        return $this->setData(self::ID, $pk);
    }

    /**
     * @return mixed|null
     */
    public function getWebsiteId()
    {
        return $this->_getData(self::WEBSITE_ID);
    }

    /**
     * @param $websiteId
     * @return mixed|Rate
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @return mixed|null
     */
    public function getDestCountryId()
    {
        return $this->_getData(self::DEST_COUNTRY_ID);
    }

    /**
     * @param $destCountryId
     * @return mixed|Rate
     */
    public function setDestCountryId($destCountryId)
    {
        return $this->setData(self::DEST_COUNTRY_ID, $destCountryId);
    }

    /**
     * @return mixed|null
     */
    public function getDestRegionId()
    {
        return $this->_getData(self::DEST_REGION_ID);
    }

    /**
     * @param $destRegionId
     * @return mixed|Rate
     */
    public function setDestRegionId($destRegionId)
    {
        return $this->setData(self::DEST_REGION_ID, $destRegionId);
    }

    /**
     * @return mixed|null
     */
    public function getDestCity()
    {
        return $this->_getData(self::DEST_CITY);
    }

    /**
     * @param $destCity
     * @return mixed|Rate
     */
    public function setDestCity($destCity)
    {
        return $this->setData(self::DEST_CITY, $destCity);
    }

    /**
     * @return mixed|null
     */
    public function getDestZip()
    {
        return $this->_getData(self::DEST_ZIP);
    }

    /**
     * @param $destZip
     * @return mixed|Rate
     */
    public function setDestZip($destZip)
    {
        return $this->setData(self::DEST_ZIP, $destZip);
    }

    /**
     * @return mixed|null
     */
    public function getDestZipTo()
    {
        return $this->_getData(self::DEST_ZIP_TO);
    }

    /**
     * @param $destZipTo
     * @return mixed|Rate
     */
    public function setDestZipTo($destZipTo)
    {
        return $this->setData(self::DEST_ZIP_TO, $destZipTo);
    }

    /**
     * @return mixed|null
     */
    public function getConditionName()
    {
        return $this->_getData(self::CONDITION_NAME);
    }

    /**
     * @param $conditionName
     * @return mixed|Rate
     */
    public function setConditionName($conditionName)
    {
        return $this->setData(self::CONDITION_NAME, $conditionName);
    }

    /**
     * @return mixed|null
     */
    public function getConditionFromValue()
    {
        return $this->_getData(self::CONDITION_FROM_VALUE);
    }

    /**
     * @param $conditionFromValue
     * @return mixed|Rate
     */
    public function setConditionFromValue($conditionFromValue)
    {
        return $this->setData(self::CONDITION_FROM_VALUE, $conditionFromValue);
    }

    /**
     * @return mixed|null
     */
    public function getConditionToValue()
    {
        return $this->_getData(self::CONDITION_TO_VALUE);
    }

    /**
     * @param $conditionToValue
     * @return mixed|Rate
     */
    public function setConditionToValue($conditionToValue)
    {
        return $this->setData(self::CONDITION_TO_VALUE, $conditionToValue);
    }

    /**
     * @return mixed|null
     */
    public function getPrice()
    {
        return $this->_getData(self::PRICE);
    }

    /**
     * @param $price
     * @return mixed|Rate
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return mixed|null
     */
    public function getCost()
    {
        return $this->_getData(self::COST);
    }

    /**
     * @param $cost
     * @return mixed|Rate
     */
    public function setCost($cost)
    {
        return $this->setData(self::COST, $cost);
    }

    /**
     * @return mixed|null
     */
    public function getShippingMethod()
    {
        return $this->_getData(self::SHIPPING_METHOD);
    }

    /**
     * @param $shippingMethod
     * @return mixed|Rate
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }
}

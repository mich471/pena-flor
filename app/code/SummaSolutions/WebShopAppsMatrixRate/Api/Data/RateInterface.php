<?php
namespace SummaSolutions\WebShopAppsMatrixRate\Api\Data;

interface RateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'pk';
    const WEBSITE_ID = 'website_id';
    const DEST_COUNTRY_ID = 'dest_country_id';
    const DEST_REGION_ID = 'dest_region_id';
    const DEST_CITY = 'dest_city';
    const DEST_ZIP = 'dest_zip';
    const DEST_ZIP_TO = 'dest_zip_to';
    const CONDITION_NAME = 'condition_name';
    const CONDITION_FROM_VALUE = 'condition_from_value';
    const CONDITION_TO_VALUE = 'condition_to_value';
    const PRICE = 'price';
    const COST = 'cost';
    const SHIPPING_METHOD = 'shipping_method';

    /**
     * @return mixed
     */
    public function getPk();

    /**
     * @param $pk
     * @return mixed
     */
    public function setPk($pk);

    /**
     * @return mixed
     */
    public function getWebsiteId();

    /**
     * @param $websiteId
     * @return mixed
     */
    public function setWebsiteId($websiteId);

    /**
     * @return mixed
     */
    public function getDestCountryId();

    /**
     * @param $destCountryId
     * @return mixed
     */
    public function setDestCountryId($destCountryId);

    /**
     * @return mixed
     */
    public function getDestRegionId();

    /**
     * @param $destRegionId
     * @return mixed
     */
    public function setDestRegionId($destRegionId);

    /**
     * @return mixed
     */
    public function getDestCity();

    /**
     * @param $destCity
     * @return mixed
     */
    public function setDestCity($destCity);

    /**
     * @return mixed
     */
    public function getDestZip();

    /**
     * @param $destZip
     * @return mixed
     */
    public function setDestZip($destZip);

    /**
     * @return mixed
     */
    public function getDestZipTo();

    /**
     * @param $destZipTo
     * @return mixed
     */
    public function setDestZipTo($destZipTo);

    /**
     * @return mixed
     */
    public function getConditionName();

    /**
     * @param $conditionName
     * @return mixed
     */
    public function setConditionName($conditionName);

    /**
     * @return mixed
     */
    public function getConditionFromValue();

    /**
     * @param $conditionFromValue
     * @return mixed
     */
    public function setConditionFromValue($conditionFromValue);

    /**
     * @return mixed
     */
    public function getConditionToValue();

    /**
     * @param $conditionToValue
     * @return mixed
     */
    public function setConditionToValue($conditionToValue);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @param $price
     * @return mixed
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getCost();

    /**
     * @param $cost
     * @return mixed
     */
    public function setCost($cost);

    /**
     * @return mixed
     */
    public function getShippingMethod();

    /**
     * @param $shippingMethod
     * @return mixed
     */
    public function setShippingMethod($shippingMethod);
}

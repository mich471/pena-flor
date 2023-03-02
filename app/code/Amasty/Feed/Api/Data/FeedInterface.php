<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Api\Data;

interface FeedInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';
    public const NAME = 'name';
    public const FILENAME = 'filename';
    public const FEED_TYPE = 'feed_type';
    public const IS_ACTIVE = 'is_active';
    public const STORE_ID = 'store_id';
    public const EXECUTE_MODE = 'execute_mode';
    public const CRON_TIME = 'cron_time';
    public const CSV_COLUMN_NAME = 'csv_column_name';
    public const CSV_HEADER = 'csv_header';
    public const CSV_ENCLOSURE = 'csv_enclosure';
    public const CSV_DELIMITER = 'csv_delimiter';
    public const FORMAT_PRICE_CURRENCY = 'format_price_currency';
    public const CSV_FIELD = 'csv_field';
    public const XML_HEADER = 'xml_header';
    public const XML_ITEM = 'xml_item';
    public const XML_CONTENT = 'xml_content';
    public const XML_FOOTER = 'xml_footer';
    public const FORMAT_PRICE_CURRENCY_SHOW = 'format_price_currency_show';
    public const FORMAT_PRICE_DECIMALS = 'format_price_decimals';
    public const FORMAT_PRICE_DECIMAL_POINT = 'format_price_decimal_point';
    public const FORMAT_PRICE_THOUSANDS_SEPARATOR = 'format_price_thousands_separator';
    public const FORMAT_DATE = 'format_date';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';
    public const GENERATED_AT = 'generated_at';
    public const DELIVERY_ENABLED = 'delivery_enabled';
    public const DELIVERY_HOST = 'delivery_host';
    public const DELIVERY_TYPE = 'delivery_type';
    public const DELIVERY_USER = 'delivery_user';
    public const DELIVERY_PASSWORD = 'delivery_password';
    public const DELIVERY_PATH = 'delivery_path';
    public const DELIVERY_PASSIVE_MODE = 'delivery_passive_mode';
    public const UTM_SOURCE = 'utm_source';
    public const UTM_MEDIUM = 'utm_medium';
    public const UTM_TERM = 'utm_term';
    public const UTM_CONTENT = 'utm_content';
    public const UTM_CAMPAIGN = 'utm_campaign';
    public const IS_TEMPLATE = 'is_template';
    public const COMPRESS = 'compress';
    public const EXCLUDE_DISABLED = 'exclude_disabled';
    public const EXCLUDE_SUBDISABLED = 'exclude_subdisabled';
    public const EXCLUDE_OUT_OF_STOCK = 'exclude_out_of_stock';
    public const EXCLUDE_NOT_VISIBLE = 'exclude_not_visible';
    public const CRON_DAY = 'cron_day';
    public const PRODUCTS_AMOUNT = 'products_amount';
    public const GENERATION_TYPE = 'generation_type';
    public const STATUS = 'status';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getFilename();

    /**
     * @param string|null $filename
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFilename($filename);

    /**
     * @return string|null
     */
    public function getFeedType();

    /**
     * @param string|null $feedType
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFeedType($feedType);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int|null $storeId
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getExecuteMode();

    /**
     * @param string $executeMode
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setExecuteMode($executeMode);

    /**
     * @return string|null
     */
    public function getCronTime();

    /**
     * @param string|null $cronTime
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCronTime($cronTime);

    /**
     * @return int
     */
    public function getCsvColumnName();

    /**
     * @param int $csvColumnName
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCsvColumnName($csvColumnName);

    /**
     * @return string|null
     */
    public function getCsvHeader();

    /**
     * @param string|null $csvHeader
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCsvHeader($csvHeader);

    /**
     * @return string|null
     */
    public function getCsvEnclosure();

    /**
     * @param string|null $csvEnclosure
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCsvEnclosure($csvEnclosure);

    /**
     * @return string|null
     */
    public function getCsvDelimiter();

    /**
     * @param string|null $csvDelimiter
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCsvDelimiter($csvDelimiter);

    /**
     * @return string|null
     */
    public function getFormatPriceCurrency();

    /**
     * @param string|null $formatPriceCurrency
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatPriceCurrency($formatPriceCurrency);

    /**
     * @return string|null
     */
    public function getCsvField();

    /**
     * @param string|null $csvField
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCsvField($csvField);

    /**
     * @return string|null
     */
    public function getXmlHeader();

    /**
     * @param string|null $xmlHeader
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setXmlHeader($xmlHeader);

    /**
     * @return string|null
     */
    public function getXmlItem();

    /**
     * @param string|null $xmlItem
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setXmlItem($xmlItem);

    /**
     * @return string|null
     */
    public function getXmlContent();

    /**
     * @param string|null $xmlContent
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setXmlContent($xmlContent);

    /**
     * @return string|null
     */
    public function getXmlFooter();

    /**
     * @param string|null $xmlFooter
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setXmlFooter($xmlFooter);

    /**
     * @return int
     */
    public function getFormatPriceCurrencyShow();

    /**
     * @param int $formatPriceCurrencyShow
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatPriceCurrencyShow($formatPriceCurrencyShow);

    /**
     * @return string
     */
    public function getFormatPriceDecimals();

    /**
     * @param string $formatPriceDecimals
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatPriceDecimals($formatPriceDecimals);

    /**
     * @return string
     */
    public function getFormatPriceDecimalPoint();

    /**
     * @param string $formatPriceDecimalPoint
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatPriceDecimalPoint($formatPriceDecimalPoint);

    /**
     * @return string
     */
    public function getFormatPriceThousandsSeparator();

    /**
     * @param string $formatPriceThousandsSeparator
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatPriceThousandsSeparator($formatPriceThousandsSeparator);

    /**
     * @return string|null
     */
    public function getFormatDate();

    /**
     * @param string|null $formatDate
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setFormatDate($formatDate);

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @param string|null $conditionsSerialized
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @return string|null
     */
    public function getGeneratedAt();

    /**
     * @param string|null $generatedAt
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setGeneratedAt($generatedAt);

    /**
     * @return int
     */
    public function getDeliveryEnabled();

    /**
     * @param int $deliveryEnabled
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryEnabled($deliveryEnabled);

    /**
     * @return string|null
     */
    public function getDeliveryHost();

    /**
     * @param string|null $deliveryHost
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryHost($deliveryHost);

    /**
     * @return string|null
     */
    public function getDeliveryType();

    /**
     * @param string|null $deliveryType
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryType($deliveryType);

    /**
     * @return string|null
     */
    public function getDeliveryUser();

    /**
     * @param string|null $deliveryUser
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryUser($deliveryUser);

    /**
     * @return string|null
     */
    public function getDeliveryPassword();

    /**
     * @param string|null $deliveryPassword
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryPassword($deliveryPassword);

    /**
     * @return string|null
     */
    public function getDeliveryPath();

    /**
     * @param string|null $deliveryPath
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryPath($deliveryPath);

    /**
     * @return int
     */
    public function getDeliveryPassiveMode();

    /**
     * @param int $deliveryPassiveMode
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setDeliveryPassiveMode($deliveryPassiveMode);

    /**
     * @return string|null
     */
    public function getUtmSource();

    /**
     * @param string|null $utmSource
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setUtmSource($utmSource);

    /**
     * @return string|null
     */
    public function getUtmMedium();

    /**
     * @param string|null $utmMedium
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setUtmMedium($utmMedium);

    /**
     * @return string|null
     */
    public function getUtmTerm();

    /**
     * @param string|null $utmTerm
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setUtmTerm($utmTerm);

    /**
     * @return string|null
     */
    public function getUtmContent();

    /**
     * @param string|null $utmContent
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setUtmContent($utmContent);

    /**
     * @return string|null
     */
    public function getUtmCampaign();

    /**
     * @param string|null $utmCampaign
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setUtmCampaign($utmCampaign);

    /**
     * @return int
     */
    public function getIsTemplate();

    /**
     * @param int $isTemplate
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setIsTemplate($isTemplate);

    /**
     * @return string
     */
    public function getCompress();

    /**
     * @param string $compress
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCompress($compress);

    /**
     * @return int
     */
    public function getExcludeDisabled();

    /**
     * @param int $excludeDisabled
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setExcludeDisabled($excludeDisabled);

    /**
     * @return int
     */
    public function getExcludeSubDisabled();

    /**
     * @param int $excludeSubDisabled
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setExcludeSubDisabled($excludeSubDisabled);

    /**
     * @return int
     */
    public function getExcludeOutOfStock();

    /**
     * @param int $excludeOutOfStock
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setExcludeOutOfStock($excludeOutOfStock);

    /**
     * @return int
     */
    public function getExcludeNotVisible();

    /**
     * @param int $excludeNotVisible
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setExcludeNotVisible($excludeNotVisible);

    /**
     * @return string|null
     */
    public function getCronDay();

    /**
     * @param string|null $cronDay
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setCronDay($cronDay);

    /**
     * @return int
     */
    public function getProductsAmount();

    /**
     * @param int $productsAmount
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setProductsAmount($productsAmount);

    /**
     * @return string
     */
    public function getGenerationType();

    /**
     * @param string $generationType
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setGenerationType($generationType);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\Feed\Api\Data\FeedInterface
     */
    public function setStatus($status);
}

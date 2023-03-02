<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model;

use Amasty\Feed\Api\Data\FeedInterface;
use Magento\Framework\Model\AbstractModel;

class Feed extends AbstractModel implements FeedInterface
{
    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Feed\Model\ResourceModel\Feed $resource,
        \Amasty\Feed\Model\ResourceModel\Feed\Collection $resourceCollection,
        \Amasty\Base\Model\Serializer $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct()
    {
        $this->_init(\Amasty\Feed\Model\ResourceModel\Feed::class);
        $this->setIdFieldName(FeedInterface::ENTITY_ID);
    }

    /**
     * @return bool
     */
    public function isCsv()
    {
        return $this->getFeedType() == 'txt' || $this->getFeedType() == 'csv';
    }

    /**
     * @return bool
     */
    public function isXml()
    {
        return $this->getFeedType() == 'xml';
    }

    public function getCsvField()
    {
        $ret = $this->getData(FeedInterface::CSV_FIELD);

        if (!is_array($ret)) {
            $config = $this->serializer->unserialize($ret);
            $ret = [];

            if (is_array($config)) {
                foreach ($config as $item) {
                    $ret[] = [
                        'header' => isset($item['header']) ? $item['header'] : '',
                        'attribute' => isset($item['attribute']) ? $item['attribute'] : null,
                        'static_text' => isset($item['static_text']) ? $item['static_text'] : null,
                        'format' => isset($item['format']) ? $item['format'] : '',
                        'parent' => isset($item['parent']) ? $item['parent'] : '',
                        'modify' => isset($item['modify']) ? $item['modify'] : [],
                    ];
                }
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getUtmParams()
    {
        $ret = [];

        if ($this->getUtmSource()) {
            $ret['utm_source'] = $this->getUtmSource();
        }

        if ($this->getUtmMedium()) {
            $ret['utm_medium'] = $this->getUtmMedium();
        }

        if ($this->getUtmTerm()) {
            $ret['utm_term'] = $this->getUtmTerm();
        }

        if ($this->getUtmContent()) {
            $ret['utm_content'] = $this->getUtmContent();
        }

        if ($this->getUtmCampaign()) {
            $ret['utm_campaign'] = $this->getUtmCampaign();
        }

        return $ret;
    }

    public function getFilename()
    {
        $ret = $this->_getData(FeedInterface::FILENAME);
        $ext = '.' . $this->getFeedType();

        if (strpos($ret, $ext) === false) {
            $ret .= $ext;
        }

        return $ret;
    }

    public function getConditionsSerialized()
    {
        $conditionsSerialized = $this->getData(FeedInterface::CONDITIONS_SERIALIZED);

        if ($conditionsSerialized) {
            if ($conditionsSerialized[0] == 'a') { // Old serialization format used
                // New version of Magento
                if (interface_exists(\Magento\Framework\Serialize\SerializerInterface::class)) {
                    $conditionsSerialized = $this->serializer->serialize(
                        $this->serializer->unserialize($conditionsSerialized)
                    );
                }
            }
        }

        return $conditionsSerialized;
    }

    public function getEntityId()
    {
        return $this->_getData(FeedInterface::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        $this->setData(FeedInterface::ENTITY_ID, $entityId);

        return $this;
    }

    public function getName()
    {
        return $this->_getData(FeedInterface::NAME);
    }

    public function setName($name)
    {
        $this->setData(FeedInterface::NAME, $name);

        return $this;
    }

    public function setFilename($filename)
    {
        $this->setData(FeedInterface::FILENAME, $filename);

        return $this;
    }

    public function getFeedType()
    {
        return $this->_getData(FeedInterface::FEED_TYPE);
    }

    public function setFeedType($feedType)
    {
        $this->setData(FeedInterface::FEED_TYPE, $feedType);

        return $this;
    }

    public function getIsActive()
    {
        return $this->_getData(FeedInterface::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        $this->setData(FeedInterface::IS_ACTIVE, $isActive);

        return $this;
    }

    public function getStoreId()
    {
        return $this->_getData(FeedInterface::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        $this->setData(FeedInterface::STORE_ID, $storeId);

        return $this;
    }

    public function getExecuteMode()
    {
        return $this->_getData(FeedInterface::EXECUTE_MODE);
    }

    public function setExecuteMode($executeMode)
    {
        $this->setData(FeedInterface::EXECUTE_MODE, $executeMode);

        return $this;
    }

    public function getCronTime()
    {
        return $this->_getData(FeedInterface::CRON_TIME);
    }

    public function setCronTime($cronTime)
    {
        $this->setData(FeedInterface::CRON_TIME, $cronTime);

        return $this;
    }

    public function getCsvColumnName()
    {
        return $this->_getData(FeedInterface::CSV_COLUMN_NAME);
    }

    public function setCsvColumnName($csvColumnName)
    {
        $this->setData(FeedInterface::CSV_COLUMN_NAME, $csvColumnName);

        return $this;
    }

    public function getCsvHeader()
    {
        return $this->_getData(FeedInterface::CSV_HEADER);
    }

    public function setCsvHeader($csvHeader)
    {
        $this->setData(FeedInterface::CSV_HEADER, $csvHeader);

        return $this;
    }

    public function getCsvEnclosure()
    {
        return $this->_getData(FeedInterface::CSV_ENCLOSURE);
    }

    public function setCsvEnclosure($csvEnclosure)
    {
        $this->setData(FeedInterface::CSV_ENCLOSURE, $csvEnclosure);

        return $this;
    }

    public function getCsvDelimiter()
    {
        return $this->_getData(FeedInterface::CSV_DELIMITER);
    }

    public function setCsvDelimiter($csvDelimiter)
    {
        $this->setData(FeedInterface::CSV_DELIMITER, $csvDelimiter);

        return $this;
    }

    public function getFormatPriceCurrency()
    {
        return $this->_getData(FeedInterface::FORMAT_PRICE_CURRENCY);
    }

    public function setFormatPriceCurrency($formatPriceCurrency)
    {
        $this->setData(FeedInterface::FORMAT_PRICE_CURRENCY, $formatPriceCurrency);

        return $this;
    }

    public function setCsvField($csvField)
    {
        $this->setData(FeedInterface::CSV_FIELD, $csvField);

        return $this;
    }

    public function getXmlHeader()
    {
        return $this->_getData(FeedInterface::XML_HEADER);
    }

    public function setXmlHeader($xmlHeader)
    {
        $this->setData(FeedInterface::XML_HEADER, $xmlHeader);

        return $this;
    }

    public function getXmlItem()
    {
        return $this->_getData(FeedInterface::XML_ITEM);
    }

    public function setXmlItem($xmlItem)
    {
        $this->setData(FeedInterface::XML_ITEM, $xmlItem);

        return $this;
    }

    public function getXmlContent()
    {
        return $this->_getData(FeedInterface::XML_CONTENT);
    }

    public function setXmlContent($xmlContent)
    {
        $this->setData(FeedInterface::XML_CONTENT, $xmlContent);

        return $this;
    }

    public function getXmlFooter()
    {
        return $this->_getData(FeedInterface::XML_FOOTER);
    }

    public function setXmlFooter($xmlFooter)
    {
        $this->setData(FeedInterface::XML_FOOTER, $xmlFooter);

        return $this;
    }

    public function getFormatPriceCurrencyShow()
    {
        return $this->_getData(FeedInterface::FORMAT_PRICE_CURRENCY_SHOW);
    }

    public function setFormatPriceCurrencyShow($formatPriceCurrencyShow)
    {
        $this->setData(FeedInterface::FORMAT_PRICE_CURRENCY_SHOW, $formatPriceCurrencyShow);

        return $this;
    }

    public function getFormatPriceDecimals()
    {
        return $this->_getData(FeedInterface::FORMAT_PRICE_DECIMALS);
    }

    public function setFormatPriceDecimals($formatPriceDecimals)
    {
        $this->setData(FeedInterface::FORMAT_PRICE_DECIMALS, $formatPriceDecimals);

        return $this;
    }

    public function getFormatPriceDecimalPoint()
    {
        return $this->_getData(FeedInterface::FORMAT_PRICE_DECIMAL_POINT);
    }

    public function setFormatPriceDecimalPoint($formatPriceDecimalPoint)
    {
        $this->setData(FeedInterface::FORMAT_PRICE_DECIMAL_POINT, $formatPriceDecimalPoint);

        return $this;
    }

    public function getFormatPriceThousandsSeparator()
    {
        return $this->_getData(FeedInterface::FORMAT_PRICE_THOUSANDS_SEPARATOR);
    }

    public function setFormatPriceThousandsSeparator($formatPriceThousandsSeparator)
    {
        $this->setData(FeedInterface::FORMAT_PRICE_THOUSANDS_SEPARATOR, $formatPriceThousandsSeparator);

        return $this;
    }

    public function getFormatDate()
    {
        return $this->_getData(FeedInterface::FORMAT_DATE);
    }

    public function setFormatDate($formatDate)
    {
        $this->setData(FeedInterface::FORMAT_DATE, $formatDate);

        return $this;
    }

    public function setConditionsSerialized($conditionsSerialized)
    {
        $this->setData(FeedInterface::CONDITIONS_SERIALIZED, $conditionsSerialized);

        return $this;
    }

    public function getGeneratedAt()
    {
        return $this->_getData(FeedInterface::GENERATED_AT);
    }

    public function setGeneratedAt($generatedAt)
    {
        $this->setData(FeedInterface::GENERATED_AT, $generatedAt);

        return $this;
    }

    public function getDeliveryEnabled()
    {
        return $this->_getData(FeedInterface::DELIVERY_ENABLED);
    }

    public function setDeliveryEnabled($deliveryEnabled)
    {
        $this->setData(FeedInterface::DELIVERY_ENABLED, $deliveryEnabled);

        return $this;
    }

    public function getDeliveryHost()
    {
        return $this->_getData(FeedInterface::DELIVERY_HOST);
    }

    public function setDeliveryHost($deliveryHost)
    {
        $this->setData(FeedInterface::DELIVERY_HOST, $deliveryHost);

        return $this;
    }

    public function getDeliveryType()
    {
        return $this->_getData(FeedInterface::DELIVERY_TYPE);
    }

    public function setDeliveryType($deliveryType)
    {
        $this->setData(FeedInterface::DELIVERY_TYPE, $deliveryType);

        return $this;
    }

    public function getDeliveryUser()
    {
        return $this->_getData(FeedInterface::DELIVERY_USER);
    }

    public function setDeliveryUser($deliveryUser)
    {
        $this->setData(FeedInterface::DELIVERY_USER, $deliveryUser);

        return $this;
    }

    public function getDeliveryPassword()
    {
        return $this->_getData(FeedInterface::DELIVERY_PASSWORD);
    }

    public function setDeliveryPassword($deliveryPassword)
    {
        $this->setData(FeedInterface::DELIVERY_PASSWORD, $deliveryPassword);

        return $this;
    }

    public function getDeliveryPath()
    {
        return $this->_getData(FeedInterface::DELIVERY_PATH);
    }

    public function setDeliveryPath($deliveryPath)
    {
        $this->setData(FeedInterface::DELIVERY_PATH, $deliveryPath);

        return $this;
    }

    public function getDeliveryPassiveMode()
    {
        return $this->_getData(FeedInterface::DELIVERY_PASSIVE_MODE);
    }

    public function setDeliveryPassiveMode($deliveryPassiveMode)
    {
        $this->setData(FeedInterface::DELIVERY_PASSIVE_MODE, $deliveryPassiveMode);

        return $this;
    }

    public function getUtmSource()
    {
        return $this->_getData(FeedInterface::UTM_SOURCE);
    }

    public function setUtmSource($utmSource)
    {
        $this->setData(FeedInterface::UTM_SOURCE, $utmSource);

        return $this;
    }

    public function getUtmMedium()
    {
        return $this->_getData(FeedInterface::UTM_MEDIUM);
    }

    public function setUtmMedium($utmMedium)
    {
        $this->setData(FeedInterface::UTM_MEDIUM, $utmMedium);

        return $this;
    }

    public function getUtmTerm()
    {
        return $this->_getData(FeedInterface::UTM_TERM);
    }

    public function setUtmTerm($utmTerm)
    {
        $this->setData(FeedInterface::UTM_TERM, $utmTerm);

        return $this;
    }

    public function getUtmContent()
    {
        return $this->_getData(FeedInterface::UTM_CONTENT);
    }

    public function setUtmContent($utmContent)
    {
        $this->setData(FeedInterface::UTM_CONTENT, $utmContent);

        return $this;
    }

    public function getUtmCampaign()
    {
        return $this->_getData(FeedInterface::UTM_CAMPAIGN);
    }

    public function setUtmCampaign($utmCampaign)
    {
        $this->setData(FeedInterface::UTM_CAMPAIGN, $utmCampaign);

        return $this;
    }

    public function getIsTemplate()
    {
        return $this->_getData(FeedInterface::IS_TEMPLATE);
    }

    public function setIsTemplate($isTemplate)
    {
        $this->setData(FeedInterface::IS_TEMPLATE, $isTemplate);

        return $this;
    }

    public function getCompress()
    {
        return $this->_getData(FeedInterface::COMPRESS);
    }

    public function setCompress($compress)
    {
        $this->setData(FeedInterface::COMPRESS, $compress);

        return $this;
    }

    public function getExcludeDisabled()
    {
        return $this->_getData(FeedInterface::EXCLUDE_DISABLED);
    }

    public function setExcludeDisabled($excludeDisabled)
    {
        $this->setData(FeedInterface::EXCLUDE_DISABLED, $excludeDisabled);

        return $this;
    }

    public function getExcludeSubDisabled()
    {
        return (int)$this->_getData(FeedInterface::EXCLUDE_SUBDISABLED);
    }

    public function setExcludeSubDisabled($excludeSubDisabled)
    {
        $this->setData(FeedInterface::EXCLUDE_SUBDISABLED, $excludeSubDisabled);

        return $this;
    }

    public function getExcludeOutOfStock()
    {
        return $this->_getData(FeedInterface::EXCLUDE_OUT_OF_STOCK);
    }

    public function setExcludeOutOfStock($excludeOutOfStock)
    {
        $this->setData(FeedInterface::EXCLUDE_OUT_OF_STOCK, $excludeOutOfStock);

        return $this;
    }

    public function getExcludeNotVisible()
    {
        return $this->_getData(FeedInterface::EXCLUDE_NOT_VISIBLE);
    }

    public function setExcludeNotVisible($excludeNotVisible)
    {
        $this->setData(FeedInterface::EXCLUDE_NOT_VISIBLE, $excludeNotVisible);

        return $this;
    }

    public function getCronDay()
    {
        return $this->_getData(FeedInterface::CRON_DAY);
    }

    public function setCronDay($cronDay)
    {
        $this->setData(FeedInterface::CRON_DAY, $cronDay);

        return $this;
    }

    public function getProductsAmount()
    {
        return $this->_getData(FeedInterface::PRODUCTS_AMOUNT);
    }

    public function setProductsAmount($productsAmount)
    {
        $this->setData(FeedInterface::PRODUCTS_AMOUNT, $productsAmount);

        return $this;
    }

    public function getGenerationType()
    {
        return $this->_getData(FeedInterface::GENERATION_TYPE);
    }

    public function setGenerationType($generationType)
    {
        $this->setData(FeedInterface::GENERATION_TYPE, $generationType);

        return $this;
    }

    public function getStatus()
    {
        return $this->_getData(FeedInterface::STATUS);
    }

    public function setStatus($status)
    {
        $this->setData(FeedInterface::STATUS, $status);

        return $this;
    }
}

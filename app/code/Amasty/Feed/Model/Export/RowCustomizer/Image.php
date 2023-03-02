<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product;
use Magento\Framework\UrlInterface;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as ProductSitemap;
use Magento\Store\Model\StoreManagerInterface;

class Image implements RowCustomizerInterface
{
    public const THUMBNAIL_TYPE = 'thumbnail';

    public const IMAGE_TYPE = 'image';

    public const SMALL_IMAGE_TYPE = 'small_image';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $urlPrefix;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        $this->urlPrefix = $this->storeManager->getStore($collection->getStoreId())
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'catalog/product';
    }

    /**
     * @inheritdoc
     */
    public function addHeaderColumns($columns)
    {
        return $columns;
    }

    /**
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        $customData = &$dataRow['amasty_custom_data'];

        $customData[Product::PREFIX_IMAGE_ATTRIBUTE] = [
            self::THUMBNAIL_TYPE => $this->checkImage($dataRow, self::THUMBNAIL_TYPE),
            self::IMAGE_TYPE => $this->checkImage($dataRow, self::IMAGE_TYPE),
            self::SMALL_IMAGE_TYPE => $this->checkImage($dataRow, self::SMALL_IMAGE_TYPE),
        ];

        return $dataRow;
    }

    /**
     * @param array $dataRow
     * @param string $imgType
     *
     * @return string|null
     */
    private function checkImage($dataRow, $imgType)
    {
        if (isset($dataRow[$imgType])
            && $dataRow[$imgType] !== ProductSitemap::NOT_SELECTED_IMAGE
        ) {
            $dataRow[$imgType] = '/' . ltrim($dataRow[$imgType], '/');

            return $this->urlPrefix . $dataRow[$imgType];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }
}

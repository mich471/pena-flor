<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\Framework\UrlInterface;

class Gallery implements RowCustomizerInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $urlPrefix;

    /**
     * @var array
     */
    protected $gallery = [];

    /**
     * @var Product
     */
    protected $export;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\ResourceConnection $resource,
        Product $export
    ) {
        $this->storeManager = $storeManager;
        $this->export = $export;
        $this->productMetadata = $productMetadata;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        if ($this->export->hasAttributes(Product::PREFIX_GALLERY_ATTRIBUTE)) {
            $this->urlPrefix = $this->storeManager->getStore($collection->getStoreId())
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product';

            $this->gallery = $this->export->getMediaGallery($productIds);
        }
    }

    /**
     * @return array
     */
    public function getGallery()
    {
        return $this->gallery;
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
        $productId = $this->convertEntityIdToRowIdIfNeed((int)$productId);
        $customData = &$dataRow['amasty_custom_data'];
        $gallery = $this->getGallery();
        $gallery = $gallery[$productId] ?? [];
        $galleryImg = [];

        foreach ($gallery as $key => $data) {
            $data['_media_image'] = '/' . ltrim($data['_media_image'], '/');

            if (!isset($customData['image'])
                || !in_array($this->urlPrefix . $data['_media_image'], $customData['image'])
            ) {
                $galleryImg[] = $this->urlPrefix . $data['_media_image'];
            }
        }

        $customData[Product::PREFIX_GALLERY_ATTRIBUTE] = [
            'image_1' => isset($galleryImg[0]) ? $galleryImg[0] : null,
            'image_2' => isset($galleryImg[1]) ? $galleryImg[1] : null,
            'image_3' => isset($galleryImg[2]) ? $galleryImg[2] : null,
            'image_4' => isset($galleryImg[3]) ? $galleryImg[3] : null,
            'image_5' => isset($galleryImg[4]) ? $galleryImg[4] : null,
        ];

        return $dataRow;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }

    protected function convertEntityIdToRowIdIfNeed(int $id): int
    {
        if ($this->productMetadata->getEdition() == 'Community') {
            return $id;
        }

        $tableName = $this->resource->getTableName('catalog_product_entity');
        $select = $this->connection->select()
            ->from($tableName, ['row_id'])
            ->where('entity_id = ?', $id)
            ->group('row_id');
        $result = $this->connection->fetchOne($select);

        return (int)$result;
    }
}

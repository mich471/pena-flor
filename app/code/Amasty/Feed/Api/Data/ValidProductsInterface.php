<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Api\Data;

interface ValidProductsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';
    public const FEED_ID = 'feed_id';
    public const VALID_PRODUCT_ID = 'valid_product_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $feedId
     *
     * @return \Amasty\Feed\Api\Data\ValidProductsInterface
     */
    public function setEntityId($feedId);

    /**
     * @return int
     */
    public function getFeedId();

    /**
     * @param int $feedId
     *
     * @return \Amasty\Feed\Api\Data\ValidProductsInterface
     */
    public function setFeedId($feedId);

    /**
     * @return int
     */
    public function getValidProductId();

    /**
     * @param string $validProducts
     *
     * @return \Amasty\Feed\Api\Data\ValidProductsInterface
     */
    public function setValidProductId($validProducts);
}

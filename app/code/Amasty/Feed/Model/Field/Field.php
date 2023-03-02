<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Field;

class Field extends \Magento\Framework\Model\AbstractModel
{
    public const FEED_FIELD_ID = 'feed_field_id';

    protected function _construct()
    {
        $this->_init(ResourceModel\Field::class);
        $this->setIdFieldName(self::FEED_FIELD_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }
}

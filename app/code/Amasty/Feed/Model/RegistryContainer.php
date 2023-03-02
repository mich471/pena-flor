<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model;

class RegistryContainer
{
    public const TYPE_ATTRIBUTE = 'attribute';
    public const TYPE_CUSTOM_FIELD = 'custom_field';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_IMAGE = 'image';
    public const TYPE_TEXT = 'text';

    public const VAR_STEP = 'amfeed_step';
    public const VAR_CATEGORY_MAPPER = 'amfeed_category_mapper';
    public const VAR_IDENTIFIER_EXISTS = 'amfeed_identifier_exists';
    public const VAR_FEED = 'amfeed_id';

    public const VALUE_FIRST_STEP = 1;
    public const VALUE_LAST_STEP = 6;

    public const MAX_ADDITIONAL_IMAGES = 5;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Set value in core registry
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setValue($key, $value)
    {
        $this->registry->register($key, $value);
    }

    /**
     * Get value from core registry
     *
     * @param mixed $key
     * @return mixed
     */
    public function getValue($key)
    {
        return $this->registry->registry($key);
    }
}

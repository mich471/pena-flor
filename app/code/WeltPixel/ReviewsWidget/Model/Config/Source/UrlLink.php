<?php
namespace WeltPixel\ReviewsWidget\Model\Config\Source;

/**
 * Class UrlLink
 * @package WeltPixel\ReviewsWidget\Model\Config\Source
 */
class UrlLink implements \Magento\Framework\Option\ArrayInterface
{
    const OPTION_CURRENTPAGE = 1;
    const OPTION_PRODUCTPAGE = 2;
    const OPTION_CUSTOMURL = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::OPTION_CURRENTPAGE, 'label' => __('Current Page')],
            ['value' => self::OPTION_PRODUCTPAGE, 'label' => __('Product Page')],
            ['value' => self::OPTION_CUSTOMURL, 'label' => __('Custom Url')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::OPTION_CURRENTPAGE => __('Current Page'),
            self::OPTION_PRODUCTPAGE => __('Product Page'),
            self::OPTION_CUSTOMURL => __('Custom Url')
        ];
    }
}

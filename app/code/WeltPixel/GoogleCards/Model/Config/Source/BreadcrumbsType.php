<?php

namespace WeltPixel\GoogleCards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class BreadcrumbsType
 *
 * @package WeltPixel\GoogleCards\Model\Config\Source
 */
class BreadcrumbsType implements ArrayInterface
{
    const BREADCRUMB_DEFAULT = 'default';
    const BREADCRUMB_FULL = 'full';

    /**
     * Return list of Description Options
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::BREADCRUMB_DEFAULT,
                'label' => __('Default Magento behavior')
            ],
            [
                'value' => self::BREADCRUMB_FULL,
                'label' => __('Always process the full breadcrumbs')
            ]

        ];
    }
}

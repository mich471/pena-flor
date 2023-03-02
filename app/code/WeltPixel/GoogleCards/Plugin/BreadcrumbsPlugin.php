<?php

namespace WeltPixel\GoogleCards\Plugin;

use \Magento\Theme\Block\Html\Breadcrumbs;

/**
 * Class BreadcrumbsPlugin
 * @package WeltPixel\GoogleCards\Plugin
 */
class BreadcrumbsPlugin
{
    /**
     * @var \WeltPixel\GoogleCards\Model\BreadcrumbStorage
     */
    protected $_breadcrumbStorage;

    /**
     * @var array
     */
    protected $breadcrumbsData = [];

    /**
     * BreadcrumbsPlugin constructor.
     * @param \WeltPixel\GoogleCards\Model\BreadcrumbStorage $breadcrumbStorage
     */
    public function __construct(
        \WeltPixel\GoogleCards\Model\BreadcrumbStorage $breadcrumbStorage
    )
    {
        $this->_breadcrumbStorage = $breadcrumbStorage;
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @param $crumbName
     * @param $crumbInfo
     * @return array
     */
    public function beforeAddCrumb(Breadcrumbs $breadcrumbs, $crumbName, $crumbInfo)
    {
        if (isset($crumbInfo['label']) && (isset($crumbInfo['link']) && !empty($crumbInfo['link']))) {
            $crumbData = [];
            $label = is_object($crumbInfo['label']) ? $crumbInfo['label']->getText() : $crumbInfo['label'];
            $crumbData['label'] = $label;
            $crumbData['link'] = $crumbInfo['link'];
            if (!in_array($label, array_column($this->breadcrumbsData, 'label'))) {
                array_push($this->breadcrumbsData, $crumbData);
            }

        }
        $this->_breadcrumbStorage->setBreadcrumbData($this->breadcrumbsData);
        return [
            $crumbName,
            $crumbInfo,
        ];
    }

}

<?php

namespace WeltPixel\GoogleCards\ViewModel\Product;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Breadcrumbs extends DataObject  implements ArgumentInterface
{
    /**
     * @param $breadcrumbs
     * @return $this
     */
    public function setBreadCrumbs($breadcrumbs)
    {
        $this->setData('breadcrumbs', $breadcrumbs);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getBreadCrumbs()
    {
        return $this->getData('breadcrumbs');
    }
}

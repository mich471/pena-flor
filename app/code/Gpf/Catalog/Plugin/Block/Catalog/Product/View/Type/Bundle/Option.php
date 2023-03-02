<?php

namespace Gpf\Catalog\Plugin\Block\Catalog\Product\View\Type\Bundle;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option as BundleOption;

class Option
{
    
    public function aroundGetSelectionTitlePrice(BundleOption $option, callable $proceed, $selection, $includeContainer = true)
    {
        $result = $proceed($selection, $includeContainer);
        if (!is_null($selection->getData('varietal'))) {
            $result = str_replace("&nbsp;", '</br><span class="product-name">'
                .$selection->getResource()->getAttribute('varietal')->getFrontend()->getValue($selection).
                '</span> &nbsp;',
                $result
            );
        }
        return $result;
    }

}
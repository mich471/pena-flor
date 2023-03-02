<?php

namespace WeltPixel\LayeredNavigation\Block\Adminhtml\System\Config;

/**
 * Implement
 * @category WeltPixel_OwlCarouselSlider
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel_OwlCarouselSlider Developer
 */
class InfoMessage extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $number = (int)substr($element->getId(), -1);


        $html = '
            <div class="message" style="text-align: center; margin-top: 20px;">
                <p>' . __('For more settings on attribute level, go to
                    <strong>Stores -> Attributes -> Product -> [select desired attribute from grid] ->
                    Storefront Properties -> WeltPixel Layered Navigation Properties</strong>
                     section.') . '</p>
            </div>
        ';

        return $html;
    }
}

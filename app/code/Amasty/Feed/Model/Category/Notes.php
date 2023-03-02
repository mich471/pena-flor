<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Category;

class Notes
{
    /**
     * @var string
     */
    public static $href = '<a target="_blank" href="https://support.google.com/merchants/answer/1705911?hl=en">'
        . 'Google Taxonomy</a>';

    /**
     * @var string
     */
    public static $mappingNote = 'Please check %1 and rename your categories to match the corresponding'
        . ' Google categories according to the requirements.<br/> <b>Important!</b> You should define'
        . ' the full path of the category exactly as it is in the taxonomy.'
        . ' For instance, if you are trying to associate your Shorts category with Google\'s,'
        . ' you might rename it to "Apparel & Accessories > Clothing > Shorts".';

    /**
     * @var string
     */
    public static $excludeNote = 'Carefully review all the categories listed below and select those you want to exclude'
        . ' from your product feed by checking the corresponding checkbox(es). Excluded categories'
        . ' will not be mapped to Google Taxonomies and won\'t be included in the generated feed.';

    /**
     * @return \Magento\Framework\Phrase
     */
    public function translateMappingNote()
    {
        return __('Please check $1 and rename your categories to match the corresponding'
            . ' Google categories according to the requirements.<br/> <b>Important!</b> You should define'
            . ' the full path of the category exactly as it is in the taxonomy.'
            . ' For instance, if you are trying to associate your Shorts category with Google\'s,'
            . ' you might rename it to "Apparel & Accessories > Clothing > Shorts".');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function translateExcludeNote()
    {
        return __('Carefully review all the categories listed below and select those you want to exclude'
            . ' from your product feed by checking the corresponding checkbox(es). Excluded categories'
            . ' will not be mapped to Google Taxonomies and won\'t be included in the generated feed.');
    }
}

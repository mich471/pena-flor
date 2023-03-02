<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Tax extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'g:rate';

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_OTHER_ATTRIBUTES . '|tax_percents';

    /**
     * @var string
     */
    protected $name = 'tax';

    /**
     * @var string
     */
    protected $description = 'The tax rate as a percent of the item price, i.e., a number as a percentage';

    /**
     * @var bool
     */
    protected $required = true;

    /**
     * @var string
     */
    protected $template = '<g:tax>
    <g:country>::country</g:country>
    <:tag>{attribute=":value" format=":format" parent=":parent" optional=":optional" modify=":modify"}</:tag>
    <g:tax_ship>y</g:tax_ship>
</g:tax>' . PHP_EOL;

    protected function getEvaluateData()
    {
        $data = parent::getEvaluateData();
        $data['::country'] = $this->direcotryData->getDefaultCountry();

        return $data;
    }
}

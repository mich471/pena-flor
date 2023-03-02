<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Link extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'link';

    /**
     * @var int
     */
    protected $limit = 2000;

    /**
     * @var string
     */
    protected $modify = 'html_escape';

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_URL_ATTRIBUTE . '|with_category';

    /**
     * @var string
     */
    protected $name =  'link';

    /**
     * @var string
     */
    protected $description = "URL directly linking to your item's page on your website";

    /**
     * @var bool
     */
    protected $required = true;

    /**
     * Get tag values
     *
     * @return array
     */
    protected function getEvaluateData()
    {
        return [
            ":tag" => $this->getTag(),
            ":value" => $this->getValue(),
            ":format" => $this->getFormat(),
            ":optional" => $this->getOptional(),
            ":modify" => $this->getModify(),
            ":parent" => 'yes'
        ];
    }
}

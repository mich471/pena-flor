<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Title extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'title';

    /**
     * @var int
     */
    protected $limit = 150;

    /**
     * @var string
     */
    protected $modify = 'html_escape';

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_PRODUCT_ATTRIBUTE . '|name';

    /**
     * @var bool
     */
    protected $required = true;

    /**
     * @var string
     */
    protected $name = 'title';

    /**
     * @var string
     */
    protected $description = 'Title of the item';

    public function getModify()
    {
        $modify = $this->modify;
        if ($this->limit) {
            $modify .= '|length:' . $this->limit;
        }

        return $modify;
    }
}

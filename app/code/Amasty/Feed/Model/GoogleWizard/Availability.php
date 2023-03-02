<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Availability extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'g:availability';

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var string
     */
    protected $modify = "replace:1^In Stock|replace:0^Out of Stock";

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_INVENTORY_ATTRIBUTE . '|is_in_stock';

    /**
     * @var string
     */
    protected $name = 'availability';

    /**
     * @var string
     */
    protected $description = 'Availability status of the item';

    /**
     * @var bool
     */
    protected $required = true;
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard\Price;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Effectivedate extends \Amasty\Feed\Model\GoogleWizard\Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'g:sale_price_effective_date';

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_OTHER_ATTRIBUTES . '|sale_price_effective_date';

    /**
     * @var string
     */
    protected $name = 'sale price effective date';

    /**
     * @var string
     */
    protected $description = 'Date range during which the item is on sale';

    /**
     * @var int
     */
    protected $limit = 71;
}

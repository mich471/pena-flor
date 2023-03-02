<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

class Gtin extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'g:gtin';

    /**
     * @var string
     */
    protected $modify = 'html_escape';

    /**
     * @var string
     */
    protected $name = 'gtin';

    /**
     * @var string
     */
    protected $description = 'Global Trade Item Number (GTIN) of the item<br/>Please check'
        . ' <a target="_blank" href="https://support.google.com/merchants/answer/6219078?hl=en">here</a>'
        . ' for details on GTIN and MPN';

    /**
     * @var int
     */
    protected $limit = 50;
}

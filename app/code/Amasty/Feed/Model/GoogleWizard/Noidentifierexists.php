<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\RegistryContainer;

class Noidentifierexists extends Element
{
    /**
     * @var string
     */
    protected $type = RegistryContainer::TYPE_CUSTOM_FIELD;

    /**
     * @var string
     */
    protected $tag = 'g:identifier_exists';

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var string
     */
    protected $value = 'FALSE';

    /**
     * @var string
     */
    protected $template = '<:tag>:value</:tag>' . PHP_EOL;
}

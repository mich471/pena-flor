<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;
use Amasty\Feed\Model\RegistryContainer;

class Image extends Element
{
    /**
     * @var string
     */
    protected $type = RegistryContainer::TYPE_ATTRIBUTE;

    /**
     * @var string
     */
    protected $tag = 'g:image_link';

    /**
     * @var int
     */
    protected $limit = 2000;

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var string
     */
    protected $value = ExportProduct::PREFIX_IMAGE_ATTRIBUTE . '|thumbnail';

    /**
     * @var string
     */
    protected $name = 'image link';

    /**
     * @var string
     */
    protected $description = 'URL of an image of the item';

    /**
     * @var bool
     */
    protected $required = true;
}

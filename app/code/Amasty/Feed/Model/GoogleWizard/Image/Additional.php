<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard\Image;

use Amasty\Feed\Model\Export\Product as ExportProduct;

class Additional extends \Amasty\Feed\Model\GoogleWizard\Element
{
    /**
     * @var string
     */
    protected $type = 'images';

    /**
     * @var string
     */
    protected $tag = 'g:additional_image_link';

    /**
     * @var string
     */
    protected $name = 'additional image link';

    /**
     * @var string
     */
    protected $description = 'Additional URLs of images of the item';

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var int
     */
    protected $limit = 2000;

    public function setImageIdx($idx)
    {
        $this->value = 'image_' . $idx;

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getModify()
    {
        return $this->modify . ':' . $this->value;
    }

    protected function getEvaluateData()
    {
        $value = strtolower($this->getValue());
        $value = ExportProduct::PREFIX_GALLERY_ATTRIBUTE . '|' . $value;
        $this->setValue($value);

        return parent::getEvaluateData();
    }
}

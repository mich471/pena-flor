<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\GoogleWizard;

class Condition extends Element
{
    /**
     * @var string
     */
    protected $type = 'attribute';

    /**
     * @var string
     */
    protected $tag = 'g:condition';

    /**
     * @var string
     */
    protected $format = 'as_is';

    /**
     * @var bool
     */
    protected $required = true;

    /**
     * @var string
     */
    protected $name = 'condition';

    /**
     * @var string
     */
    protected $value = 'new';

    /**
     * @var string
     */
    protected $description = 'Condition or state of the item (allowed values: new, refubrished, used)';

    /**
     * @var string
     */
    protected $template = '<:tag>:value</:tag>' . PHP_EOL;

    public function getValue()
    {
        $value = parent::getValue();

        return strtolower($value);
    }

    protected function getEvaluateData()
    {
        return [
            ":tag"      => $this->getTag(),
            ":value"    => $this->getValue()
        ];
    }
}

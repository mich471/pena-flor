<?php

namespace Lc\SalesSequence\Model;


use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\SalesSequence\Model\Meta;
use Magento\Framework\DB\Sequence\SequenceInterface;

class Sequence extends \Magento\SalesSequence\Model\Sequence implements SequenceInterface
{

    protected $currentPattern = "%s%'.06d%s";

    public function __construct(Meta $meta, AppResource $resource, $pattern = \Magento\SalesSequence\Model\Sequence::DEFAULT_PATTERN)
    {
        if (!is_null($this->currentPattern)) {
            $pattern = $this->currentPattern;
        }
        \Magento\SalesSequence\Model\Sequence::__construct($meta, $resource, $pattern);
    }

}

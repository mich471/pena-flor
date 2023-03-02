<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\Adapter;

use Magento\Framework\Exception\LocalizedException;

class AdapterProvider
{
    /**
     * @var array
     */
    private $adapters;

    public function __construct($adapters)
    {
        $this->adapters = $adapters;
    }

    public function get($adapterName, $params)
    {
        if (!isset($this->adapters[$adapterName])) {
            throw new LocalizedException(__('Please correct the file format.'));
        }

        return $this->adapters[$adapterName]->create($params);
    }
}

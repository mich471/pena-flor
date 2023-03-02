<?php
/**
 * Created by PhpStorm.
 * User: jgimenez
 * Date: 21/03/2019
 * Time: 09:50
 */

namespace Gpf\Catalog\Model\Product\Attribute\Source;


class Package extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['value' => '0', 'label' => __('Simple')],
            ['value' => '1', 'label' => __('Box')],
            ['value' => '2', 'label' => __('POP')],
        ];
        return $this->_options;
    }
}
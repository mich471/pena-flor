<?php

namespace Gpf\Sales\Block\Order;

use Magento\Sales\Block\Order\Totals as MagentoTotals;

class Totals extends MagentoTotals
{

    const SUBTOTAL_WITH_TAX = "subtotal_with_tax";

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, array $data = [])
    {
        parent::__construct($context, $registry, $data);
    }

    protected function _initTotals()
    {
        $source = $this->getSource();
        $_totals = [];
        $_totals[self::SUBTOTAL_WITH_TAX] = new \Magento\Framework\DataObject(
            ['code' => self::SUBTOTAL_WITH_TAX, 'value' => $source->getSubtotalInclTax(), 'label' => __('Subtotal')]
        );
        parent::_initTotals();

        $this->_totals = array_merge($_totals, $this->_totals);
        return $this;
    }


    /**
     * get totals array for visualization
     *
     * @param array|null $area
     * @param boolean $tax
     * @return array
     */
    public function getTotals($area = null, $tax = false)
    {
        if (!$tax && isset($this->_totals[self::SUBTOTAL_WITH_TAX])) {
            unset($this->_totals[self::SUBTOTAL_WITH_TAX]);
        } else {
            unset($this->_totals['subtotal']);
            unset($this->_totals['tax']);
        }

        $totals = [];
        if ($area === null) {
            $totals = $this->_totals;
        } else {
            $area = (string)$area;
            foreach ($this->_totals as $total) {
                $totalArea = (string)$total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }
}
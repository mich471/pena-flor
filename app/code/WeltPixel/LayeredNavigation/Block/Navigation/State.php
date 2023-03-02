<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */
namespace WeltPixel\LayeredNavigation\Block\Navigation;

/**
 * Layered navigation state
 *
 * @api
 * @since 100.0.2
 */
class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * @var array
     */
    protected $ratesStep = [
         '0-20' => 1,
         '21-40' => 2,
         '41-60' => 3,
         '61-80' => 4,
         '81-100' => 5,
    ];

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_wpHelper = $wpHelper;
        parent::__construct($context,$layerResolver, $data);

        if($this->_wpHelper->isEnabled()) {
            $this->_template = 'WeltPixel_LayeredNavigation::layer/state.phtml';
        }
    }

    /**
     * @param $filter
     * @return string
     */
    public function getRateFilterLabel($filter) {
        $selectedValue = $filter->getValue();
        $countStars = 1;
        if(is_array($selectedValue)) {
            $val = $selectedValue[0].'-'.$selectedValue[1];
            $countStars = $this->ratesStep[$val];
        } else {
            $valArr = explode('-', $selectedValue);
            $val = $valArr[0].'-'.$valArr[1];
            $countStars = $this->ratesStep[$val];
        }
        $label = '<div class="rating-summary" style="display: inline-block;margin-top: -5px;">
                    <div class="rating-result" title="up to '.$countStars*20 .'%"">
                        <span style="width:'.$countStars*20 .'%"></span>
                    </div>
                </div>';

        return $label;
    }

    /**
     * @return string
     */
    public function getCategoryParamLabel() {
        return $this->_wpHelper->getCategoryParamLabel();
    }

    /**
     * @return string
     */
    public function getRatingParamLabel() {
        return $this->_wpHelper->getRatingParamLabel();
    }

}

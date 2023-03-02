<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_{Module}
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */


namespace WeltPixel\SocialLogin\Block\Adminhtml\Dashboard;


class Analytics extends \Magento\Backend\Block\Template
{

    /**
     * @var \WeltPixel\SocialLogin\Model\Analytics
     */
    protected $analitycs;
    protected $report;

    /**
     * @var
     */
    protected $_priceCurrency;

    /**
     * Analytics constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \WeltPixel\SocialLogin\Model\Analytics $analitycs
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \WeltPixel\SocialLogin\Model\Analytics $analitycs,
        \WeltPixel\SocialLogin\Model\Report $report,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->analitycs = $analitycs;
        $this->report = $report;
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @return array
     */
    public function fetchAnalyticsData() {

        return $this->report->getAnalyticsData();
    }

    /**
     * @return mixed
     */
    public function fetchAnalyticsTotals() {

        return $this->report->getAnalyticsTotals();
    }

    /**
     * @return mixed
     */
    public function getPriceCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }

    public function getRefreshUrl() {
        return $this->getUrl('sociallogin/dashboard/refresh');
    }

    /**
     * @return string
     */
    public function getLastUpdate() {
        return $this->report->getLastUpdate();
    }

}
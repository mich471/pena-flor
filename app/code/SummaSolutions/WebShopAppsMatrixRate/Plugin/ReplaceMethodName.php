<?php

namespace SummaSolutions\WebShopAppsMatrixRate\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use WebShopApps\MatrixRate\Model\Carrier\Matrixrate;
use SummaSolutions\WebShopAppsMatrixRate\Helper\Config;

class ReplaceMethodName
{
    const METHOD_PREFIX = 'matrixrate_';

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Matrixrate $subject
     * @param Result $result
     * @param RateRequest $request
     * @return Result
     */
    public function afterCollectRates(Matrixrate $subject, Result $result, RateRequest $request): Result
    {
        $rates = $result->getAllRates();

        foreach($rates as $rate) {
            if (str_contains($rate->getMethod(), self::METHOD_PREFIX )) {
                $this->applyLabel($rate);
            }
        }

        return $result;
    }

    /**
     * @param $rate
     * @return mixed
     */
    protected function applyLabel($rate)
    {
        try {
            $rate->setMethodTitle($this->config->getLabel($rate->getMethodTitle()->render()));
        } catch (NoSuchEntityException $e) {
            $rate->setMethodTitle('');
        }

        return $rate;
    }
}

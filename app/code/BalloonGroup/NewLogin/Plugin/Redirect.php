<?php

namespace BalloonGroup\NewLogin\Plugin;

use Closure;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Redirect
{
    /**
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultFactory;

    /**
     * @param Registry $registry
     * @param UrlInterface $url
     * @param ResultFactory $resultFactory
     */
    public function __construct(Registry $registry, UrlInterface $url, ResultFactory $resultFactory)
    {
        $this->coreRegistry = $registry;
        $this->url = $url;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param $subject
     * @param Closure $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     */
    public function aroundGetRedirect($subject, Closure $proceed)
    {
        if ($this->coreRegistry->registry('is_new_account')) {
            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $result->setUrl($this->url->getUrl('newlogin/register/success'));
            return $result;
        }

        return $proceed();
    }
}

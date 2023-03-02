<?php

namespace WeltPixel\UserProfile\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * Class Router
 * @package WeltPixel\UserProfile\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'profile/user') !== false) {
            $pathParams = explode('/', $identifier);
            $username = (isset($pathParams['2'])) ? $pathParams[2] : '';
            $request->setModuleName('profile')->setControllerName('view')->setActionName('index')->setParam('username', $username);
        } else {
            return false;
        }

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
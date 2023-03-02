<?php
namespace WeltPixel\SocialLogin\Controller\Ajax;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Twitteroauth
 * @package WeltPixel\SocialLogin\Controller\Ajax
 */
class Twitteroauth extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \WeltPixel\SocialLogin\Model\Twitter
     */
    protected $twitterModel;


    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Twitteroauth constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \WeltPixel\SocialLogin\Model\Twitter $twitterModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \WeltPixel\SocialLogin\Model\Twitter $twitterModel
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->twitterModel = $twitterModel;
    }

    public function execute()
    {
        $response = [
            'success' => false,
            'oauthLink' => ''
        ];

        if ($this->getRequest()->isPost()) {
            $twitterOauthUrl = $this->twitterModel->getTwiterLink();
            if (strlen($twitterOauthUrl)) {
                $response['oauthLink'] = $twitterOauthUrl;
                $response['success'] = true;
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $result = $resultJson->setData($response);
        return $result;
    }
}
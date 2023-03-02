<?php
namespace WeltPixel\AdvancedWishlist\Controller\View;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Collections
 * @package WeltPixel\AdvancedWishlisy\Controller\View
 */
class Editable extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Editable constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface
     */
    public function execute()
    {
        $result = [
            'editable' => false
        ];

        $profileCustomerId = $this->getRequest()->getParam('profile_customer_id');
        $loggedInUserId = $this->customerSession->getCustomerId();

        if ($loggedInUserId == $profileCustomerId) {
            $result['editable'] = true;
        }

        return $this->prepareResult($result);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}

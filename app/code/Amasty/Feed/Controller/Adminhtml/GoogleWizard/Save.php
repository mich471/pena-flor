<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Controller\Adminhtml\GoogleWizard;

use Amasty\Feed\Model\RegistryContainer;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Amasty\Feed\Controller\Adminhtml\AbstractGoogleWizard
{
    /**
     * @var \Amasty\Feed\Model\GoogleWizard
     */
    private $googleWizard;

    /**
     * @var array
     */
    private $configSetup = [];

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Amasty\Feed\Model\GoogleWizard $googleWizard,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        parent::__construct($context);

        $this->googleWizard = $googleWizard;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->preparePostData();
            $args = [];

            try {
                $this->configSetup = $this->googleWizard->setup($data);

                $categoryMapperId = RegistryContainer::VAR_CATEGORY_MAPPER;
                $args[$categoryMapperId] = $this->getConfigValue($categoryMapperId);

                $feedId = RegistryContainer::VAR_FEED;
                $args[$feedId] = $this->getConfigValue($feedId);

                if ($this->getRequest()->getParam('setup_complete')) {
                    $this->googleWizard->clearSessionData();
                    $feedId = $args[RegistryContainer::VAR_FEED];
                    $arguments = [
                        'id' => $feedId
                    ];

                    if ($this->getRequest()->getParam('force_generate')) {
                        $arguments['_fragment'] = 'forcegenerate';

                        return $this->resultRedirectFactory->create()->setPath('amfeed/feed/edit', $arguments);
                    }

                    return $this->resultRedirectFactory->create()->setPath('amfeed/feed/index');
                } else {
                    return $this->resultRedirectFactory->create()->setPath('amfeed/feed/index', $args);
                }
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the Google Feed. Please review the error log.')
                );

                return $this->resultRedirectFactory->create()->setPath('amfeed/googleWizard/index');
            }
        }

        return $this->resultRedirectFactory->create()->setPath('amfeed/feed/index');
    }

    /**
     * Get prepared POST
     *
     * @return array
     */
    private function preparePostData()
    {
        $data = [];
        if ($this->getRequest()->getPostValue()) {
            $postData = $this->getRequest()->getPostValue();

            $postDataKeys = array_keys($postData);
            $data = array_combine($postDataKeys, $postData);

            if (isset($data['delivery_password'])) {
                $data['delivery_password'] = $this->encryptor->encrypt($data['delivery_password']);
            }
        }

        return $data;
    }

    private function getConfigValue($key)
    {
        $value = '';
        if (isset($this->configSetup[$key])) {
            $value = $this->configSetup[$key];
        }

        return $value;
    }
}

<?php

namespace WeltPixel\UserProfile\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use WeltPixel\UserProfile\Helper\Data as ProfileHelper;
use Magento\Framework\Data\Form\FormKey\Validator;
use WeltPixel\UserProfile\Model\UserProfileFactory;
use WeltPixel\UserProfile\Model\UserProfileFields;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;

abstract class Account extends Action
{

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var ProfileHelper
     */
    protected $profileHelper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ImageAdapterFactory
     */
    protected $imageFactory;

    /**
     * Account constructor.
     * @param RedirectFactory $resultRedirectFactory
     * @param PageFactory $pageFactory
     * @param Validator $formKeyValidator
     * @param Context $context
     * @param ProfileHelper $profileHelper
     * @param UserProfileFactory $userProfileFactory
     * @param UserProfileFields $userProfileFields
     * @param UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param ImageAdapterFactory $imageFactory
     */
    public function __construct(
        RedirectFactory $resultRedirectFactory,
        PageFactory $pageFactory,
        Validator $formKeyValidator,
        Context $context,
        ProfileHelper $profileHelper,
        UserProfileFactory $userProfileFactory,
        UserProfileFields $userProfileFields,
        UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        ImageAdapterFactory $imageFactory
    )
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->pageFactory = $pageFactory;
        $this->profileHelper = $profileHelper;
        $this->formKeyValidator = $formKeyValidator;
        $this->userProfileFactory = $userProfileFactory;
        $this->userProfileFields = $userProfileFields;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function _initModuleConfiguration()
    {
        if (!$this->profileHelper->isEnabled()) {
            return $this->_redirect('/');
        }
    }

}
<?php

namespace WeltPixel\UserProfile\Controller\View;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File as FileSystemIoFile;
use Psr\Log\LoggerInterface;
use WeltPixel\UserProfile\Model\UserProfile;
use WeltPixel\UserProfile\Model\UserProfileFactory;
use WeltPixel\UserProfile\Helper\Renderer as ProfileRendererHelper;
use Magento\Framework\Filesystem;
use WeltPixel\UserProfile\Exception\SaveProfileException;
use WeltPixel\UserProfile\Model\UserProfileFields;


/**
 * Class Save
 * @package WeltPixel\UserProfile\Controller\View
 */
class Save extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProfileRendererHelper
     */
    protected $profileRendererHelper;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var FileSystemIoFile
     */
    protected $fileSystemIo;

    /**
     * @var integer
     */
    protected $customerId;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var array
     */
    protected  $fieldOptions;

    /**
     * Editable constructor.
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UserProfileFactory $userProfileFactory
     * @param LoggerInterface $logger
     * @param ProfileRendererHelper $profileRendererHelper
     * @param Filesystem $fileSystem
     * @param FileSystemIoFile $fileSystemIo
     * @param UserProfileFields $userProfileFields
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        UserProfileFactory $userProfileFactory,
        LoggerInterface $logger,
        ProfileRendererHelper $profileRendererHelper,
        Filesystem $fileSystem,
        FileSystemIoFile $fileSystemIo,
        UserProfileFields $userProfileFields
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->userProfileFactory = $userProfileFactory;
        $this->logger = $logger;
        $this->profileRendererHelper = $profileRendererHelper;
        $this->fileSystem = $fileSystem;
        $this->fileSystemIo = $fileSystemIo;
        $this->userProfileFields = $userProfileFields;
        $this->fieldOptions = null;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $response = [
            'result' => false,
            'redirect' => false
        ];

        $postData = $this->getRequest()->getPostValue();
        if (!$this->getRequest()->isPost() || !$postData['profileId']) {
            return $this->prepareResult($response);
        }

        $profileId = $postData['profileId'];
        $customerId = $postData['customerId'];
        $this->customerId = $customerId;
        try {
            $profileModel = $this->userProfileFactory->create()->load($profileId);
            if ($profileModel->getCustomerId() != $customerId) {
                $response['msg'] = __('User cannot edit this profile');
                return $this->prepareResult($response);
            }

            $validationErrors = [];

            foreach ($postData as $fieldName => $value) {
                if (in_array($fieldName, ['profileId', 'customerId'])) continue;
                $parsedValue = $this->parseField($fieldName, $value);

                $validationResult = $this->validateField($profileModel, $fieldName, $parsedValue);
                if ($validationResult['valid']) {
                    $profileModel->setData($fieldName, $parsedValue);
                }
                if ($validationResult['error']) {
                    $validationErrors[] = $validationResult['error'];
                }
            }

            if (count($validationErrors)) {
                $errorMessages = "<p>" . implode("</p><p>", $validationErrors) . '</p>';
                throw new SaveProfileException($errorMessages);
            }

            $profileModel->save();
            $response['result'] = true;
            $response['profileName'] = $this->profileRendererHelper->getProfileName($profileModel);
            $response['profileDetails'] = $this->profileRendererHelper->getProfileDetails($profileModel);

            if ($profileModel->getOrigData('username') != $profileModel->getData('username')) {
                $response['redirect'] =  $this->profileRendererHelper->getUserProfileLink($profileModel->getData('username'));
            }

            $this->_eventManager->dispatch(
                'userprofile_inlinesave_after',
                [
                    'userprofile' => $profileModel,
                    'response'  => &$response
                ]
            );
        } catch (SaveProfileException $ex) {
            $response['error'] = $ex->getMessage();
        } catch (\Exception $ex) {
            $response['error'] = __('There was a problem saving your account.');
            $this->logger->critical($ex->getMessage());
        }

        return $this->prepareResult($response);
    }

    /**
     * @param $fieldName
     * @param $value
     * @return mixed
     */
    public function parseField($fieldName, $value)
    {
        $parsedValue = $value;
        switch ($fieldName) {
            case 'username' :
                $parsedValue = strtolower($this->htmlSanitize($value, true));
                break;
            case 'first_name' :
            case 'last_name' :
            case 'location' :
            case 'dob' :
            case 'gender' :
                $parsedValue = $this->htmlSanitize($value, true);
                break;
            case 'bio' :
                $parsedValue = $this->htmlSanitize($value, false);
                break;
            case 'cover_image':
                $parsedValue = $this->parseImage($value, 'cover_image');
                break;
            case 'avatar':
                $parsedValue = $this->parseImage($value, 'avatar');
                break;

        }
        return $parsedValue;
    }

    /**
     * @param UserProfile $profileModel
     * @param string $fieldName
     * @param string $parsedValue
     * @return array
     * @throws SaveProfileException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateField($profileModel, $fieldName, $parsedValue)
    {
        $response = [
            'valid' => true,
            'error' => ''
        ];
        $fieldOptions = $this->getUserFieldsOptions();

        if (($fieldName == 'username') && ($profileModel->getUsername() != $parsedValue) ) {
            $userProfile = $this->userProfileFactory->create();
            $userProfile->loadByUsername($parsedValue);
            if ($userProfile->getId()) {
                $response['valid'] = false;
                $response['error'] = __('Username already taken.');
            } else {
                preg_match('/^[a-z]+[a-z0-9_]+$/', $parsedValue, $matches);
                if (!isset($matches[0]) || strlen($parsedValue) > UserProfile::USERNAME_MAX_LENGTH) {
                    $response['valid'] = false;
                    $response['error'] = __('Please make sure you enter a valid username. Please use only letters (a-z), numbers (0-9) or underscore (_) in this field, and the first character should be a letter and should not exceed 20 caharacters.');
                }
            }
        }

        $validationFields = [
            'first_name' => __('First Name is a required field.'),
            'last_name' => __('Last Name is a required field.'),
            'gender' => __('Gender is a required field.'),
            'location' => __('Location is a required field.'),
            'dob' => __('Date of Birth is a required field.'),
            'bio' => __('Biography is a required field.')
        ];

        foreach ($validationFields as $fName => $errorMsg) {
            if ( ($fieldName == $fName) && $fieldOptions[str_replace('_','',$fName)]['enabled'] && $fieldOptions[str_replace('_','',$fName)]['required'] && !strlen(trim($parsedValue))) {
                $response['error'] = $errorMsg;
                $response['valid'] = false;
            }
        }

        /** No new image was uploaded */
        if (($fieldName == 'avatar' || $fieldName == 'cover_image') && empty($parsedValue) ) {
            $response['valid'] = false;
        }

        if (($fieldName == 'avatar') && $fieldOptions['avatar']['enabled'] && $fieldOptions['avatar']['required']) {
            if (!$profileModel->getAvatar() && empty($parsedValue)) {
                $response['error'] = __('Avatar is a required field.');
            }
        }
        if (($fieldName == 'cover_image') && $fieldOptions['cover_image']['enabled'] && $fieldOptions['cover_image']['required']) {
            if (!$profileModel->getCoverImage() && empty($parsedValue)) {
                $response['error'] = __('Cover Image is a required field.');
            }
        }


        return $response;
    }

    /**
     * @return array|null
     */
    public function getUserFieldsOptions()
    {
        if (!$this->fieldOptions) {
            $this->fieldOptions = $this->userProfileFields->getFieldsOptions();
        }

        return $this->fieldOptions;
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

    /**
     * @param $str
     * @param bool $stripTags
     * @return string|string[]|null
     */
    public function htmlSanitize($str, $stripTags = true)
    {
        if ($stripTags) {
            $str = strip_tags($str);
        }
        $trimmedStr = trim($str);
        $pattern = "/<p[^>]*>[\s|\W|&nbsp;]*<\/p>/";
        $newStr = preg_replace($pattern, '', $trimmedStr);
        return $newStr;
    }

    /**
     * @param string $html
     * @param string $imageKey
     * @return string mixed
     */
    protected function parseImage($html, $imageKey)
    {
        $imageData = $this->getNewImagePath($html, $imageKey);
        $this->moveImgToNewLocation($imageData);
        return $imageData['name'];
    }

    /**
     * @param array $imgData
     * @param string $imgType
     */
    protected function moveImgToNewLocation($imgData)
    {
        $currentPath = $this->fileSystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath(UserProfile::MEDIA_IMAGES_PATH) . $imgData['path'];

        $newPath = $this->fileSystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . $imgData['name'];


        if ($this->fileSystemIo->fileExists($currentPath)) {
            $this->fileSystemIo->setAllowCreateFolders(true);
            $this->fileSystemIo->createDestinationDir(dirname($newPath));
            $this->fileSystemIo->cp($currentPath, $newPath);
            $this->fileSystemIo->rm($currentPath);
        }
    }

    /**
     * @param string $imgType
     * @return string
     */
    protected function getNewImageType($imgType)
    {
        $newPathImageType = UserProfile::MEDIA_IMAGES_PATH;
        switch ($imgType) {
            case 'cover_image' :
                $newPathImageType = UserProfile::MEDIA_COVER_PATH . DIRECTORY_SEPARATOR . $this->customerId;
                break;
            case 'avatar' :
                $newPathImageType = UserProfile::MEDIA_AVATAR_PATH . DIRECTORY_SEPARATOR . $this->customerId;
                break;
        }

        return $newPathImageType;
    }

    /**
     * @param string $html
     * @param string $imageKey
     * @return array
     */
    protected function getNewImagePath($html, $imageKey)
    {
        $image = [];
        $needle = UserProfile::MEDIA_IMAGES_PATH;
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $src = $xpath->evaluate("string(//img/@src)");
        $image['path'] = $this->getImageRelativePath($src, $needle);
        $image['name'] = '';
        $imageName = $this->getImageName('/', $image['path']);

        if ($imageName) {
            $image['name'] = $this->getNewImageType($imageKey) . DIRECTORY_SEPARATOR . $imageName;
        }

        return $image;
    }


    /**
     * @param string $str
     * @param string $needle
     * @return bool|string
     */
    protected function getImageRelativePath($str, $needle)
    {
        if (strpos($str, $needle) !== false) {
            return substr($str, strpos($str, $needle) + strlen($needle));
        }
        return false;
    }

    /**
     * @param string $needle
     * @param string $str
     * @return bool|string
     */
    protected function getImageName($needle, $str)
    {
        if (!is_bool($this->strrevpos($str, $needle))) {
            return substr($str, $this->strrevpos($str, $needle) + strlen($needle));
        }
        return false;
    }

    /**
     * @param string $instr
     * @param string $needle
     * @return bool|int
     */
    public function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) return false;
        return strlen($instr) - $rev_pos - strlen($needle);
    }


}

<?php

namespace WeltPixel\UserProfile\Controller\Account;

use Magento\Framework\Exception\LocalizedException;
use WeltPixel\UserProfile\Controller\Account as AccountAction;
use Magento\Framework\App\Filesystem\DirectoryList;
use WeltPixel\UserProfile\Exception\SaveProfileException;
use WeltPixel\UserProfile\Model\UserProfile;

/**
 * Class Edit
 * @package WeltPixel\UserProfile\Controller\Index
 */
class Edit extends AccountAction
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_initModuleConfiguration();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($validFormKey && $this->getRequest()->isPost()) {
            $profileId = $this->getRequest()->getParam('profile_id', null);
            $requestParams = $this->getRequest()->getParams();

            $userProfileModel = $this->userProfileFactory->create();
            try {
                $modelData = $this->filterRequestParams($requestParams);
                $userProfileModel->load($profileId);
                $userProfileModel->addData($modelData);
                $this->_validateFormData($userProfileModel, $modelData);
                $userProfileModel->save();
                $this->messageManager->addSuccess(__('You saved the user profile information.'));
            } catch (SaveProfileException $ex) {
                $this->messageManager->addError($ex->getMessage());
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('There was a problem saving your account.'));
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        }

        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    /**
     * @param array $requestParams
     * @return array
     */
    public function filterRequestParams($requestParams)
    {
        $result = [];
        $requiredFields = ['customer_id', 'username'];
        $modelFields = array_merge($requiredFields, $this->userProfileFields->getExistingFields());
        foreach ($modelFields as $field) {
            $mappedField = $this->getFieldInputMapper($field);
            if (isset($requestParams[$field])) {
                $result[$mappedField] = $requestParams[$field];
            }
        }

        $result['avatar'] = $this->parseAvatarImage($requestParams);
        $result['cover_image'] = $this->parseCoverImage($requestParams);

        return $result;
    }

    /**
     * @param $requestParams
     * @return string
     * @throws LocalizedException
     */
    protected function parseAvatarImage($requestParams)
    {
        try {
            $avatarUploader = $this->fileUploaderFactory->create(['fileId' => 'avatar']);
        } catch (\Exception $ex) {
            return $requestParams['avatar_current_image'];
        }
        $avatarUploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $avatarUploader->setAllowRenameFiles(false);
        $avatarUploader->setFilesDispersion(false);

        $dbPath = UserProfile::MEDIA_AVATAR_PATH . DIRECTORY_SEPARATOR . $requestParams['customer_id'];
        $avatarPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dbPath);
        $avatarUploadResult = $avatarUploader->save($avatarPath);
        if (!$avatarUploadResult) {
            throw new LocalizedException(__('Avatar image cannot be saved'));
        }

        $filePath = $avatarPath . DIRECTORY_SEPARATOR . $avatarUploadResult['file'];
        /** Resize the image if avatar was uploaded */
        try {
            $imageFactory = $this->imageFactory->create();
            $imageFactory->open($filePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->keepFrame(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize(UserProfile::MEDIA_AVATAR_WIDTH, UserProfile::MEDIA_AVATAR_HEIGHT);
            $imageFactory->save($filePath);
        } catch (\Exception $ex) {
            throw new LocalizedException(__('Avatar image could not be resized'));
        }

        return $dbPath . DIRECTORY_SEPARATOR . $avatarUploadResult['file'];
    }

    /**
     * @param $requestParams
     * @return string
     * @throws LocalizedException
     */
    protected function parseCoverImage($requestParams)
    {
        try {
            $coverUploader = $this->fileUploaderFactory->create(['fileId' => 'cover_image']);
        } catch (\Exception $ex) {
            return $requestParams['cover_image_current_image'];
        }

        $coverUploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $coverUploader->setAllowRenameFiles(false);
        $coverUploader->setFilesDispersion(false);

        $dbPath = UserProfile::MEDIA_COVER_PATH . DIRECTORY_SEPARATOR . $requestParams['customer_id'];
        $avatarPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dbPath);
        $coverUploadResult = $coverUploader->save($avatarPath);
        if (!$coverUploadResult) {
            throw new LocalizedException(__('Cover image cannot be saved'));
        }

        return $dbPath . DIRECTORY_SEPARATOR . $coverUploadResult['file'];
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFieldInputMapper($field)
    {
        $mappedField = $field;
        switch ($field) {
            case 'firstname':
                $mappedField = 'first_name';
                break;
            case 'lastname':
                $mappedField = 'last_name';
                break;
        }

        return $mappedField;
    }

    /**
     * @param  UserProfile $userProfileModel
     * @param array $modelData
     * @throws LocalizedException
     * @throws SaveProfileException
     */
    protected function _validateFormData($userProfileModel, $modelData)
    {
        $originalUsername = $userProfileModel->getOrigData('username');
        if ($originalUsername && ($originalUsername != $modelData['username'])) {
            $userProfile = $this->userProfileFactory->create();
            $userProfile->loadByUsername($modelData['username']);
            if ($userProfile->getId()) {
                throw new SaveProfileException(__('Username already taken.'));
            }
        }

        $fieldOptions = $this->userProfileFields->getFieldsOptions();
        $errors = [];

        if (!isset($modelData['username']) || (strlen($modelData['username']) > UserProfile::USERNAME_MAX_LENGTH)) {
            $errors[] = __('Please make sure you enter a valid username and it should contain less then 20 characters');
        } else {
            preg_match('/^[a-z]+[a-z0-9_]+$/', $modelData['username'], $matches);
            if (count($matches) != 1) {
                $errors[] = __('Please make sure you enter a valid username. Please use only letters (a-z), numbers (0-9) or underscore (_) in this field, and the first character should be a letter.');
            }
        }

        if ($fieldOptions['avatar']['enabled'] && $fieldOptions['avatar']['required']) {
            if (!isset($modelData['avatar']) || !strlen($modelData['avatar'])) {
                $errors[] = __('Avatar is a required field.');
            }
        }

        if ($fieldOptions['cover_image']['enabled'] && $fieldOptions['cover_image']['required']) {
            if (!isset($modelData['cover_image']) || !strlen($modelData['cover_image'])) {
                $errors[] = __('Cover Image is a required field.');
            }
        }

        if ($fieldOptions['firstname']['enabled'] && $fieldOptions['firstname']['required']) {
            if (!isset($modelData['first_name']) || !strlen($modelData['first_name'])) {
                $errors[] = __('First Name is a required field.');
            }
        }

        if ($fieldOptions['lastname']['enabled'] && $fieldOptions['lastname']['required']) {
            if (!isset($modelData['last_name']) || !strlen($modelData['last_name'])) {
                $errors[] = __('Last Name is a required field.');
            }
        }

        if ($fieldOptions['gender']['enabled'] && $fieldOptions['gender']['required']) {
            if (!isset($modelData['gender']) || !strlen($modelData['gender'])) {
                $errors[] = __('Gender is a required field.');
            }
        }

        if ($fieldOptions['location']['enabled'] && $fieldOptions['location']['required']) {
            if (!isset($modelData['location']) || !strlen($modelData['location'])) {
                $errors[] = __('Location is a required field.');
            }
        }

        if ($fieldOptions['dob']['enabled'] && $fieldOptions['dob']['required']) {
            if (!isset($modelData['dob']) || !strlen($modelData['dob'])) {
                $errors[] = __('Date of Birth is a required field.');
            }
        }


        if ($fieldOptions['bio']['enabled'] && $fieldOptions['bio']['required']) {
            if (!isset($modelData['bio']) || !strlen($modelData['bio'])) {
                $errors[] = __('Biography is a required field.');
            }
        }

        $this->_eventManager->dispatch(
            'weltpixel_userpofile_save_validation',
            [
                'userProfileModel' => $userProfileModel,
                'modelData' => $modelData,
                'errors' => $errors
            ]
        );

        if ($errors) {
            throw new SaveProfileException(implode('<br/>', $errors));
        }
    }
}

<?php

namespace WeltPixel\UserProfile\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File as FileSystemIoFile;
use Magento\Framework\Image\AdapterFactory as ImageAdapterFactory;

/**
 * Class UserProfileBuilder
 * @package WeltPixel\UserProfile\Model
 */
class UserProfileBuilder
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var UserProfileFactory
     */
    protected $userProfileFactory;

    /**
     * @var UserProfileFields
     */
    protected $userProfileFields;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var FileSystemIoFile
     */
    protected $fileSystemIo;

    /**
     * @var ImageAdapterFactory
     */
    protected $imageFactory;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserProfileFactory $userProfileFactory
     * @param UserProfileFields $userProfileFields
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param Filesystem $fileSystem
     * @param FileSystemIoFile $fileSystemIo
     * @param ImageAdapterFactory $imageFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserProfileFactory $userProfileFactory,
        UserProfileFields $userProfileFields,
        ResourceConnection $resource,
        LoggerInterface $logger,
        Filesystem $fileSystem,
        FileSystemIoFile $fileSystemIo,
        ImageAdapterFactory $imageFactory
    )
    {
        $this->customerRepository = $customerRepository;
        $this->userProfileFactory = $userProfileFactory;
        $this->userProfileFields = $userProfileFields;
        $this->logger = $logger;
        $this->fileSystem = $fileSystem;
        $this->fileSystemIo = $fileSystemIo;
        $this->imageFactory = $imageFactory;
        $this->connection = $resource->getConnection();
    }

    /**
     * @param integer $customerId
     * @param array $profileData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build($customerId, $profileData)
    {
        if (!$customerId) {
            return false;
        }

        $customer = $this->customerRepository->getById($customerId);
        if (!$customer->getId()) {
            return false;
        }

        /** @var \WeltPixel\UserProfile\Model\UserProfile $userProfile */
        $userProfile = $this->userProfileFactory->create()->loadByCustomerId($customerId);
        if ($userProfile->getId()) {
            return false;
        }

        $parsedProfileData = $this->prepareProfileData($customerId, $profileData);
        $userProfile->setData($parsedProfileData);
        try {
            $userProfile->save();
        } catch (\Exception $ex) {
            $this->logger->error(__('Could not create user profile for customer ' . $customerId));
        }
    }

    /**
     * @param integer $customerId
     * @param array $profileData
     * @return array
     */
    public function prepareProfileData($customerId, $profileData)
    {
        $parsedData = [];
        $enabledFields = array_merge(['username'], array_keys($this->userProfileFields->getEnabledFields()));
        foreach ($profileData as $key => $value) {
            $dbColumnKey = $key;
            if (in_array($key, ['first_name', 'last_name'])) { $key = str_replace('_','', $key);}
            if (!in_array($key, $enabledFields)) continue;

            $parsedData[$dbColumnKey] = $this->parseFieldValue($customerId, $dbColumnKey, $value);
        }

        $usernameAlreadyUsed = false;
        if (isset($parsedData['username'])) {
            $usernameAlreadyUsed = $this->checkUsernameAlreadyUsed($parsedData['username']);
        }

        if (!isset($parsedData['username']) || !strlen($parsedData['username']) || $usernameAlreadyUsed) {
            $parsedData['username'] = $customerId . uniqid();
        }
        $parsedData['customer_id'] = $customerId;

        return $parsedData;
    }

    /**
     * @param integer $customerId
     * @param string $dbKey
     * @param string $value
     * @return bool|string
     */
    public function parseFieldValue($customerId, $dbKey, $value)
    {
        $parsedValue = $value;
        switch ($dbKey) {
            case 'username':
                $parsedValue = substr(strtolower(preg_replace('/[^A-Za-z0-9]/', '_', trim($value))), 0, 20);
                break;
            case 'avatar':
                $parsedValue = $this->parseImageFromUrl($value, 'avatar', $customerId);
                break;
            case 'cover_image' :
                $parsedValue = $this->parseImageFromUrl($value, 'cover_image', $customerId);
                break;
            case 'gender' :
                $parsedValue = strtolower($value);
                if (!in_array($parsedValue, ['male', 'female'])) {
                    $parsedValue  = '';
                }
                break;
            default:
                $parsedValue = trim($value);
                break;
        }

        return $parsedValue;
    }

    /**
     * @param string $url
     * @param string $imageType
     * @param integer $customerId
     * @return string
     */
    protected function parseImageFromUrl($url, $imageType, $customerId)
    {
        $imageUrlSrc = false;
        if (is_array($url)) {
            $imageUrlSrc = $url['imageSrc'];
            $url = $url['imageUrl'];
        }
        $urlData = parse_url($url);
        $fileInfo = pathinfo($urlData['path']);
        if (!isset($fileInfo['extension'])) {
            return '';
        }
        $fileExt = $fileInfo['extension'];
        $fileBaseName = $fileInfo['basename'];
        $allowedImageExtensions = $this->_getAllowedExtensions();
        if (!in_array($fileExt, $allowedImageExtensions)) {
            return '';
        }

        switch ($imageType) {
            case 'cover_image' :
                $newPathImageType = UserProfile::MEDIA_COVER_PATH . DIRECTORY_SEPARATOR .$customerId;
                break;
            case 'avatar' :
                $newPathImageType = UserProfile::MEDIA_AVATAR_PATH . DIRECTORY_SEPARATOR . $customerId;
                break;
        }

        try {
            $imageDirPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($newPathImageType);
            $this->fileSystemIo->setAllowCreateFolders(true);
            $this->fileSystemIo->createDestinationDir($imageDirPath);
            if ($imageUrlSrc) {
                $image = $imageUrlSrc;
            } else {
                $image = file_get_contents($url);
            }
            $filePath = $imageDirPath . DIRECTORY_SEPARATOR . $fileBaseName;
            file_put_contents($filePath, $image);
            list($width, $height) = getimagesize($filePath);

            if (($imageType == 'avatar') && ($width > UserProfile::MEDIA_AVATAR_WIDTH) && ($height > UserProfile::MEDIA_AVATAR_HEIGHT)) {
                $imageFactory = $this->imageFactory->create();
                $imageFactory->open($filePath);
                $imageFactory->constrainOnly(true);
                $imageFactory->keepTransparency(true);
                $imageFactory->keepFrame(true);
                $imageFactory->keepAspectRatio(true);
                $imageFactory->resize(UserProfile::MEDIA_AVATAR_WIDTH, UserProfile::MEDIA_AVATAR_HEIGHT);
                $imageFactory->save($filePath);
            }
        } catch (\Exception $ex) {
            $this->logger->error("UserProfile Image save error: " . $imageType . ' '. $ex->getMessage());
            return '';
        }

        return $newPathImageType . DIRECTORY_SEPARATOR . $fileBaseName;

    }

    /**
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }

    /**
     * @param string $username
     * @return bool
     */
    protected function checkUsernameAlreadyUsed($username)
    {
        $bind = ['username' => $username];
        $select = $this->connection->select()->from(
            $this->connection->getTableName('weltpixel_user_profile'),
            ['profile_id']
        )->where(
            'username = :username'
        );

        $userProfileId = $this->connection->fetchOne($select, $bind);
        if ($userProfileId) {
            return true;
        }

        return false;
    }

}

<?php

namespace WeltPixel\UserProfile\Model;

use WeltPixel\UserProfile\Helper\Data as UserProfileHelper;

class UserProfileFields
{

    /**
     * @var UserProfileHelper
     */
    protected $userProfileHelper;

    /**
     * Data constructor.
     * @param UserProfileHelper $$userProfileHelper
     */
    public function __construct(
        UserProfileHelper $userProfileHelper
    )
    {
        $this->userProfileHelper = $userProfileHelper;
    }

    /**
     * @return array
     */
    public function getExistingFields()
    {
        return [
            'avatar',
            'cover_image',
            'firstname',
            'lastname',
            'gender',
            'location',
            'dob',
            'bio'
        ];
    }

    /**
     * @return array
     */
    public function getEnabledFields()
    {
        $fields = $this->getExistingFields();
        $enabledFields = [];

        foreach ($fields as $field) {
            if ($this->userProfileHelper->isFieldEnabled($field)) {
                $enabledFields[$field] = [
                    'required' => $this->userProfileHelper->isFieldRequired($field)
                ];
            }
        }

        return $enabledFields;
    }

    /**
     * @return array
     */
    public function getFieldsOptions()
    {
        $fields = $this->getExistingFields();
        $fieldOptions = [];

        foreach ($fields as $field) {
            $fieldOptions[$field] = [
                'required' => $this->userProfileHelper->isFieldRequired($field),
                'enabled' => $this->userProfileHelper->isFieldEnabled($field)
            ];
        }

        return $fieldOptions;
    }

}
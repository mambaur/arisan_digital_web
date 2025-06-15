<?php

namespace App\Constants;

class SettingType
{
    public const MOBILE_VERSION = 'mobile_version';
    public const IS_MAINTENANCE = 'is_maintenance';
    public const PRIVACY_POLICY_URL = 'privacy_policy_url';
    public const TIPS_URL = 'tips_url';
    public const ABOUT_URL = 'about_url';
    public const ARISAN_INFO_URL = 'arisan_info_url';
    public const TERM_AND_CONDITION_URL = 'term_and_condition_url';

    static function toArray() : array
    {
        return [
            SettingType::MOBILE_VERSION,
            SettingType::IS_MAINTENANCE,
            SettingType::PRIVACY_POLICY_URL,
            SettingType::TIPS_URL,
            SettingType::ABOUT_URL,
            SettingType::ARISAN_INFO_URL,
            SettingType::TERM_AND_CONDITION_URL,
        ];
    }

    static function validation():string
    {
        $list_string = implode(',', SettingType::toArray());
        return $list_string;
    }
}
<?php

namespace App\MemberStatusActive;

class MemberStatusActive
{
    public const ACTIVE = 'active';
    public const REJECT = 'reject';
    public const INACTIVE = 'inactive';
    public const REQUEST_JOIN = 'request_join';
    public const REQUEST_INVITATION = 'request_invitation';

    static function toArray() : array
    {
        return [
            MemberStatusActive::ACTIVE,
            MemberStatusActive::REJECT,
            MemberStatusActive::INACTIVE,
            MemberStatusActive::REQUEST_JOIN,
            MemberStatusActive::REQUEST_INVITATION,
        ];
    }

    static function validation():string
    {
        $list_string = implode(',', MemberStatusActive::toArray());
        return "in:$list_string";
    }

    static function isRequest($status):bool
    {
        return $status == MemberStatusActive::REQUEST_JOIN || $status == MemberStatusActive::REQUEST_INVITATION;
    }
}
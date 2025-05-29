<?php

namespace App\Constants;

class MemberStatusPaid
{
    public const PAID = 'paid';
    public const UNPAID = 'unpaid';
    public const SKIP = 'skip';
    public const CANCEL = 'cancel';

    static function toArray() : array
    {
        return [
            MemberStatusPaid::PAID,
            MemberStatusPaid::UNPAID,
            MemberStatusPaid::SKIP,
            MemberStatusPaid::CANCEL,
        ];
    }

    static function validation():string
    {
        $list_string = implode(',', MemberStatusPaid::toArray());
        return $list_string;
    }
}
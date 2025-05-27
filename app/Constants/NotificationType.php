<?php

namespace App\Constants;

class NotificationType
{
    // MEMBER
    public const MEMBER_INVITATION_REQUEST = 'member_invitation_request';
    public const MEMBER_INVITATION_RESPONSE = 'member_invitation_response';
    public const MEMBER_JOIN_REQUEST = 'member_join_request';
    public const MEMBER_JOIN_RESPONSE = 'member_join_response';
    public const MEMBER_REMOVAL = 'member_removal';
    public const MEMBER_PAYMENT_REMINDER = 'member_payment_reminder';
    public const MEMBER_INVITATION_REMINDER = 'member_invitation_reminder';
    
    // OWNER
    public const GROUP_OWNER_INVITATION = 'group_owner_invitation';
    public const GROUP_OWNER_REMOVAL= 'group_owner_removal';
    
    // GROUP
    public const GROUP_WINNER= 'group_winner';
}
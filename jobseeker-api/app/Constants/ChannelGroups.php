<?php

namespace App\Constants;

class ChannelGroups
{
    public const CHANNEL_GROUPS = [
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'whatsapp',
        'telegram',
        'email',
        'shared_url',
        'system',
    ];

    public static function isValidChannelGroup($value): mixed
    {
        return in_array($value, self::CHANNEL_GROUPS);
    }
}

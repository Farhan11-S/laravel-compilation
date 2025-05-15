<?php

namespace App\Enums;

enum OAuthProviderEnum: string
{
    case GITHUB = 'github';
    case FACEBOOK = 'facebook';
    case LINKEDIN = 'linkedin-openid';
    case GOOGLE = 'google';
}

<?php

namespace App\Domains\Authentication\Enums;

enum LoginStatus: string
{
    case Authenticated = 'authenticated';
    case RequiresTwoFactor = 'requires_two_factor';
    case RequiresEmailVerification = 'requires_email_verification';
}

<?php

namespace App\Domains\Authentication;

/**
 * Authentication domain boundary marker.
 *
 * Owns login, registration, password reset, email verification,
 * session management, account locking, and MFA foundation.
 */
final class AuthenticationDomain
{
    public const NAME = 'Authentication';
}

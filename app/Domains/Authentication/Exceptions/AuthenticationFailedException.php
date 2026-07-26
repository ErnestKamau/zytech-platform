<?php

namespace App\Domains\Authentication\Exceptions;

use App\Core\Exceptions\DomainException;

final class AuthenticationFailedException extends DomainException
{
    public static function invalidCredentials(): self
    {
        return new self('These credentials do not match our records.');
    }

    public static function accountLocked(?string $reason = null): self
    {
        return new self($reason ?? 'This account has been locked. Please contact support.');
    }

    public static function tooManyAttempts(int $seconds): self
    {
        return new self("Too many login attempts. Please try again in {$seconds} seconds.");
    }
}

<?php

namespace App\Domains\Authentication\Exceptions;

use App\Core\Exceptions\DomainException;

final class TwoFactorException extends DomainException
{
    public static function pendingSessionMissing(): self
    {
        return new self('Your sign-in session expired. Please log in again.');
    }

    public static function channelUnavailable(): self
    {
        return new self('That verification method is not available for your account.');
    }

    public static function sendFailed(string $message): self
    {
        return new self($message);
    }

    public static function invalidCode(): self
    {
        return new self('The verification code is invalid or has expired.');
    }

    public static function tooManyAttempts(int $seconds): self
    {
        return new self("Too many attempts. Please try again in {$seconds} seconds.");
    }

    public static function resendThrottled(int $seconds): self
    {
        return new self("Please wait {$seconds} seconds before requesting another code.");
    }
}

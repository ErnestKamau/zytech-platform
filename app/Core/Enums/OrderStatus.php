<?php

namespace App\Core\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case ReadyForFulfillment = 'ready_for_fulfillment';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::ReadyForFulfillment => 'Ready for fulfillment',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Once fulfillment has started, order lines and totals become immutable
     * (Domain Invariant: "Orders cannot be edited after fulfillment begins").
     */
    public function fulfillmentStarted(): bool
    {
        return in_array($this, [self::Processing, self::ReadyForFulfillment, self::Completed], true);
    }

    public function isCancellable(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }
}

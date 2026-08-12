<?php

namespace App\Domains\Portal\Support;

final class TicketReference
{
    public static function next(): string
    {
        return 'ZST-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}

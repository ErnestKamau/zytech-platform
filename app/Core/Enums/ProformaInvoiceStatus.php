<?php

namespace App\Core\Enums;

enum ProformaInvoiceStatus: string
{
    case Issued = 'issued';
    case Superseded = 'superseded';
    case ConvertedToInvoice = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::Issued => 'Issued',
            self::Superseded => 'Superseded',
            self::ConvertedToInvoice => 'Converted to invoice',
        };
    }
}

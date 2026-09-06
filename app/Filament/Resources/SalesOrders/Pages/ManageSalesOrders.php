<?php

namespace App\Filament\Resources\SalesOrders\Pages;

use App\Filament\Resources\SalesOrders\SalesOrderResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSalesOrders extends ManageRecords
{
    protected static string $resource = SalesOrderResource::class;
}

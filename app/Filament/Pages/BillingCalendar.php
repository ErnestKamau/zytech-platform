<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BillingCalendarWidget;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BillingCalendar extends Page
{
    protected string $view = 'filament.pages.billing-calendar';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Billing Calendar';

    public function getWidgets(): array
    {
        return [
            BillingCalendarWidget::class,
        ];
    }
}

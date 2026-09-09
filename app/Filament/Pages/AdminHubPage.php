<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\IsAdminHub;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

abstract class AdminHubPage extends Page
{
    use IsAdminHub;

    protected string $view = 'filament.pages.hub';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
}

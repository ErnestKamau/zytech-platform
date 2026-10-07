<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Http\Controllers\Admin\DocumentViewerController;
use App\Http\Controllers\Admin\InvoicePdfPreviewController;
use App\Http\Controllers\Admin\ProformaInvoicePdfPreviewController;
use App\Http\Controllers\Admin\QuotationPdfPreviewController;
use App\Http\Controllers\Admin\QuotationRequestAttachmentController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->brandName('Zytech Admin')
            ->font('Instrument Sans')
            ->colors([
                'primary' => Color::hex('#5c7349'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->plugins([
                FilamentFullCalendarPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authenticatedRoutes(function (): void {
                Route::get('/quotations/{quotation}/pdf-preview', QuotationPdfPreviewController::class)
                    ->name('quotations.pdf-preview');
                Route::get('/proforma-invoices/{proformaInvoice}/pdf-preview', ProformaInvoicePdfPreviewController::class)
                    ->name('proforma-invoices.pdf-preview');
                Route::get('/invoices/{invoice}/pdf-preview', InvoicePdfPreviewController::class)
                    ->name('invoices.pdf-preview');
                Route::get('/quotation-request-attachments/{attachment}', QuotationRequestAttachmentController::class)
                    ->name('quotation-request-attachments.download');
                Route::get('/documents/quotations/{quotation}', [DocumentViewerController::class, 'quotation'])
                    ->name('documents.quotation');
                Route::get('/documents/proforma-invoices/{proformaInvoice}', [DocumentViewerController::class, 'proforma'])
                    ->name('documents.proforma');
                Route::get('/documents/invoices/{invoice}', [DocumentViewerController::class, 'invoice'])
                    ->name('documents.invoice');
            })
            ->routes(function (): void {
                Route::get('/logout', function () {
                    return redirect()
                        ->to('/admin')
                        ->with('status', 'Use Sign out to end your session.');
                })->name('logout.get');
            });
    }
}

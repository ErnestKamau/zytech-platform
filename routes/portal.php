<?php

use App\Domains\Portal\Livewire\Dashboard;
use App\Domains\Portal\Livewire\Documents;
use App\Domains\Portal\Livewire\Meetings;
use App\Domains\Portal\Livewire\Messages;
use App\Domains\Portal\Livewire\Notifications;
use App\Domains\Portal\Livewire\Projects;
use App\Domains\Portal\Livewire\Quotations;
use App\Domains\Portal\Livewire\Support;
use App\Domains\Portal\Livewire\Timeline;
use App\Http\Controllers\Portal\PortalFileController;
use App\Http\Middleware\EnsurePortalAccess;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', EnsurePortalAccess::class])
    ->prefix('portal')
    ->name('portal.')
    ->group(function (): void {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/quotations', Quotations::class)->name('quotations');
        Route::get('/quotations/{quotation}/pdf', [PortalFileController::class, 'streamQuotationPdf'])->name('quotations.pdf');
        Route::get('/quotations/{quotation}/pdf/download', [PortalFileController::class, 'downloadQuotationPdf'])->name('quotations.pdf.download');
        Route::get('/projects', Projects::class)->name('projects');
        Route::get('/documents', Documents::class)->name('documents');
        Route::get('/documents/{document}/download', [PortalFileController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/messages', Messages::class)->name('messages');
        Route::get('/notifications', Notifications::class)->name('notifications');
        Route::get('/support', Support::class)->name('support');
        Route::get('/meetings', Meetings::class)->name('meetings');
        Route::get('/timeline', Timeline::class)->name('timeline');
    });

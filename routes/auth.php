<?php

use App\Domains\Authentication\Livewire\AccountSettings;
use App\Domains\Authentication\Livewire\ForgotPassword;
use App\Domains\Authentication\Livewire\Login;
use App\Domains\Authentication\Livewire\Profile;
use App\Domains\Authentication\Livewire\Register;
use App\Domains\Authentication\Livewire\ResetPassword;
use App\Domains\Authentication\Livewire\SecuritySettings;
use App\Domains\Authentication\Livewire\Sessions;
use App\Domains\Authentication\Livewire\VerifyEmailNotice;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', VerifyEmailNotice::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/account/profile', Profile::class)->name('account.profile');
    Route::get('/account/security', SecuritySettings::class)->name('account.security');
    Route::get('/account/sessions', Sessions::class)->name('account.sessions');
    Route::get('/account/settings', AccountSettings::class)->name('account.settings');
});

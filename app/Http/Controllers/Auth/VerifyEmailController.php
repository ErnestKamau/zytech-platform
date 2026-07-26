<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Authentication\Actions\VerifyEmail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, VerifyEmail $action): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        $action->handle($user);

        return redirect()->route('account.profile')->with('status', 'Email verified.');
    }
}

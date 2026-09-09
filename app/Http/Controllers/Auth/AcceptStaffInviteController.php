<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Authentication\Services\StaffInviteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

final class AcceptStaffInviteController extends Controller
{
    public function __invoke(string $token, StaffInviteService $invites): RedirectResponse
    {
        $user = $invites->findValidInvitee($token);

        if ($user === null) {
            return redirect()
                ->to('/admin/login')
                ->withErrors(['data.email' => 'This invite link is invalid or has expired. Ask an administrator to resend it.']);
        }

        $invites->consume($user);

        session([
            'admin_invite_email' => $user->email,
            'admin_invite_notice' => 'Welcome — sign in with the password your administrator set for you.',
        ]);

        return redirect()->to('/admin/login');
    }
}

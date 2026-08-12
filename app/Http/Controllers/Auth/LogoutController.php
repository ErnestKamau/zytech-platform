<?php

namespace App\Http\Controllers\Auth;

use App\Domains\Authentication\Actions\LogoutUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUser $action): RedirectResponse
    {
        $action->handle(Auth::user());

        return redirect()->route('login');
    }
}

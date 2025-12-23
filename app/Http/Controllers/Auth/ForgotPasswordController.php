<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form for requesting a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'We cannot find a user with that email address.',
        ]);

        // For real application
        /*
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Log the password reset attempt
        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Password reset link sent', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);
        } else {
            Log::warning('Password reset link failed', [
                'email' => $request->email,
                'status' => $status,
                'ip' => $request->ip(),
            ]);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
        */

        // For test application
        $email = $request->email;
        $user = User::where('email', $email)->firstOrFail();
        $token = Password::createToken($user);
        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $email
        ])->with('status', 'Development Mode: Password reset link generated automatically.');

    }
}
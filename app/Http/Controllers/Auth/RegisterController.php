<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterRequest $request)
    {
        $userData = $request->getUserData();
        
        // Create user with additional default data
        $user = User::create(array_merge($userData, [
            'remember_token' => Str::random(60),
            'bio' => 'New member at LaraBlog',
            'social_links' => [],
            'avatar' => null,
        ]));

        // Assign default role
        $user->assignRole('User');

        // Fire registered event
        event(new Registered($user));

        // Log successful registration
        Log::info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        // Auto-login after registration
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome to LaraBlog.');
    }
}
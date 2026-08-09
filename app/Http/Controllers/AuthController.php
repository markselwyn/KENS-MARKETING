<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle the incoming authentication request.
     */
    public function login(Request $request)
    {
        // 1. Validate form inputs
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ==========================================
        // 2. BRUTE FORCE PROTECTION (Rate Limiting)
        // ==========================================
        // Create a unique key based on their email and IP address
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        // Check if they have failed 5 times already
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Log this brute-force attack to Spatie for the audit trail
            activity()->log("SECURITY ALERT: Brute force lockout triggered for {$request->input('email')} from IP {$request->ip()}. Locked for {$seconds}s.");

            return back()->withErrors([
                'email' => "Too many login attempts. System locked. Please try again in {$seconds} seconds.",
            ])->onlyInput('email', 'remember');
        }

        $remember = $request->boolean('remember');

        // 3. Attempt login passing credentials and the remember token state
        if (Auth::attempt($credentials, $remember)) {
            
            // Success! Clear the fail counter immediately.
            RateLimiter::clear($throttleKey);

            // Regenerate session to protect against session fixation attacks
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Log successful login activity to Spatie
            activity()
                ->causedBy($user)
                ->log("User {$user->name} logged into the system as {$user->role}.");

            // 4. Role-Based Redirection Strategy
            if ($user->role === 'admin') {
                return redirect()->intended('/dashboard');
            }
            
            return redirect()->intended('/inventory');
        }

        // ==========================================
        // 5. FAILED ATTEMPT HANDLING
        // ==========================================
        // Add a strike to their fail counter
        RateLimiter::hit($throttleKey);
        
        // Log the failed attempt silently
        activity()->log("FAILED LOGIN: Attempt for {$request->input('email')} from IP {$request->ip()}.");

        // Return a generic secure failure message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our secure records.',
        ])->onlyInput('email', 'remember');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Audit log the logout before clearing session context
        if ($user) {
            activity()
                ->causedBy($user)
                ->log("User {$user->name} signed out.");
        }

        // Standard Laravel logout sequence
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirects to '/' which our web.php safely redirects straight back to '/login'
        return redirect('/');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password; // <-- ADDED FOR EMAIL RESET
use Illuminate\Auth\Events\PasswordReset; // <-- ADDED FOR EMAIL RESET
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(Request $request)
    {
        $portal = match (true) {
            $request->routeIs('admin.login') => 'Admin',
            $request->routeIs('staff.login') => 'Staff',
            default => null,
        };

        return view('login', compact('portal'));
    }

    /**
     * Show the explicit registration form for new staff.
     */
    public function showRegister()
    {
        return view('register');
    }

    /**
     * Handle Staff Registration (Creates locked accounts pending Admin approval).
     */
    public function register(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Create the locked staff account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'is_approved' => false, // STRICT LOCK: Requires Admin Approval
        ]);

        // 3. Audit Trail: Log the new signup for the Admin Hub
        activity()->log("NEW ACCOUNT REQUEST: {$user->name} ({$user->email}) registered and is awaiting approval.");

        // 4. Redirect to login with a clear notification
        return redirect()->route('login')->with('success', 'Account created! Please wait for Admin confirmation to access the system.');
    }

    /**
     * Handle the incoming authentication request.
     */
    public function login(Request $request)
    {
        // 1. Validate form inputs
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        // ==========================================
        // 2. BRUTE FORCE PROTECTION (Rate Limiting)
        // ==========================================
        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            activity()->log("SECURITY ALERT: Brute force lockout triggered for {$request->input('email')} from IP {$request->ip()}. Locked for {$seconds}s.");

            return back()->withErrors([
                'email' => "Too many login attempts. System locked. Please try again in {$seconds} seconds.",
            ])->onlyInput('email', 'remember');
        }

        $remember = $request->boolean('remember');

        // ==========================================
        // 3. ATTEMPT LOGIN & APPROVAL CHECK
        // ==========================================
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            
            // STRICT APPROVAL CHECK
            if (!Auth::user()->is_approved) {
                $wasRevoked = Auth::user()->revoked_at !== null;
                // Log that an unapproved user tried to get in
                $accountState = $wasRevoked ? 'revoked' : 'unapproved';
                activity()->causedBy(Auth::user())->log("BLOCKED LOGIN: {$accountState} user {$request->email} attempted to access the system.");
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                if ($wasRevoked) {
                    return back()->withErrors(['email' => 'Your system access has been revoked by the Administrator.']);
                }

                return back()->with('success', 'Your account is still pending. Please wait for Admin confirmation to access the system.');
            }

            // Success! Clear the fail counter immediately.
            RateLimiter::clear($throttleKey);

            // Regenerate session to protect against session fixation attacks
            $request->session()->regenerate();
            
            $authUser = Auth::user();

            // Log successful login activity to Spatie
            activity()
                ->causedBy($authUser)
                ->log("User {$authUser->name} logged into the system as {$authUser->role}.");

            // Role-Based Redirection Strategy
            if ($authUser->role === 'admin') {
                return redirect()->intended('/dashboard');
            }
            
            return redirect()->intended('/inventory');
        }

        // ==========================================
        // 4. FAILED ATTEMPT HANDLING
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

    // ==========================================
    // PASSWORD RECOVERY / FORGOT PASSWORD LOGIC
    // ==========================================

    /**
     * Display the email form to request a password reset link.
     */
    public function showForgotPassword()
    {
        return view('forgot-password');
    }

    /**
     * Send the password reset link to the user's email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            ['email' => ['required', 'email']],
            [
                'email.required' => 'Enter the email address connected to your account.',
                'email.email' => 'Enter a complete email address, such as name@example.com.',
            ]
        );

        // Laravel's built-in password broker handles generating the token and sending the email!
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display the actual form to type a new password.
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Save the newly typed password to the database.
     */
    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => [
                    'bail',
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                        $user = User::where('email', $request->input('email'))->first();

                        if ($user && Hash::check($value, $user->password)) {
                            $fail('Your new password must be different from your current password.');
                        }
                    },
                ],
            ],
            [
                'token.required' => 'This password reset link is incomplete. Request a new link and try again.',
                'email.required' => 'The reset link is missing your email address. Request a new link and try again.',
                'email.email' => 'The reset link contains an incorrectly formatted email address. Request a new link.',
                'password.required' => 'Enter a new password.',
                'password.min' => 'Your new password must contain at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match your new password.',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // If successful, send them to login. If failed, send them back to the form with errors.
        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $request->input('email'))->first();
            $loginRoute = strtolower(trim($user?->role ?? '')) === 'admin'
                ? 'admin.login'
                : 'staff.login';

            return redirect()->route($loginRoute)->with('success', 'Your password has been updated. You can now sign in.');
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'This password reset link has expired or has already been used. Request a new link.',
            Password::INVALID_USER => 'No account was found for the email address in this reset link.',
            Password::RESET_THROTTLED => 'Please wait before trying to reset your password again.',
            default => 'We could not reset your password. Request a new link and try again.',
        };

        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }
}

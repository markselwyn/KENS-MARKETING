<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // 1. INSTANT KICK-OUT: If an Admin revokes access while the user is actively logged in
            if (!$user->is_approved && $user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Audit Trail
                activity()->log("SECURITY: Kicked out suspended user {$user->email} mid-session.");
                
                return redirect()->route('login')->withErrors(['email' => 'Your system access has been temporarily revoked by the Administrator.']);
            }

            // 2. TRACK ACTIVITY: Update their 'last_seen' timestamp silently
            $user->timestamps = false; // Prevent updating the standard 'updated_at' column
            $user->last_seen = now();
            $user->saveQuietly();
        }

        return $next($request);
    }
}